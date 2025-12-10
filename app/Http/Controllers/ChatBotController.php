<?php

namespace App\Http\Controllers;

use App\Events\CreateUser;
use App\Events\DefaultData;
use App\Events\DestroyUser;
use App\Events\EditProfileUser;
use App\Events\UpdateUser;
use App\Models\EmailTemplate;
use App\Models\LoginDetail;
use App\Models\Plan;
use App\Models\ReferralTransaction;
use App\Models\Role;
use App\Models\User;
use App\Models\WorkSpace;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Auth\Events\Registered;
use Lab404\Impersonate\Impersonate;
use App\DataTables\UsersDataTable;
use App\Helpers\GraphQLGenerator;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;


class ChatBotController extends Controller
{

    public function chatbot()
    {
        if (Auth::user()->isAbleTo('user chat manage')) {
            // Generate session if not exists
            if (!session()->has('chatbot_session_id')) {
                session(['chatbot_session_id' => Str::uuid()->toString()]);
            }
            $sessionId = session('chatbot_session_id');
            // Redirect to Ai.chatbot route with session ID
            return redirect()->route('Ai.chatbot', ['chatbot_session_id' => $sessionId]);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }



    public function Aichatbot($chatbot_session_id)
    {
        session(['chatbot_session_id' => $chatbot_session_id]);

        // Fetch sessions
        $sessions = DB::table('chatbot_history')
            ->select('session', 'json_data', DB::raw('MAX(created_at) as last_active'))
            ->groupBy('session')
            ->where('user_id', auth()->id())
            ->orderByDesc('last_active')
            ->get();

        // Date references
        $now = Carbon::now()->startOfDay();
        $yesterday = $now->copy()->subDay();
        $startOfThisWeek = $now->copy()->startOfWeek(); // Monday
        $startOfLastWeek = $startOfThisWeek->copy()->subWeek();
        $endOfLastWeek = $startOfThisWeek->copy()->subDay();
        $startOfThisMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $startOfThisMonth->copy()->subMonth();
        $endOfLastMonth = $startOfThisMonth->copy()->subDay();

        // Initialize groups
        $groupedSessions = [
            'Today' => [],
            'Yesterday' => [],
            'This Week' => [],
            'Last Week' => [],
            'This Month' => [],
            'Last Month' => [],
            'Older' => []
        ];

        foreach ($sessions as $session) {
            $lastActive = Carbon::parse($session->last_active)->startOfDay();

            // Decode json_data to get last question
            $jsonData = json_decode($session->json_data, true);
            $lastQuestion = null;

            if (is_array($jsonData) && count($jsonData) > 0) {
                $lastEntry = end($jsonData);
                $lastQuestion = $lastEntry['title'] ?? null;
            }

            $session->last_question = $lastQuestion;

            // Group by date
            if ($lastActive->equalTo($now)) {
                $groupedSessions['Today'][] = $session;
            } elseif ($lastActive->equalTo($yesterday)) {
                $groupedSessions['Yesterday'][] = $session;
            } elseif ($lastActive->between($startOfThisWeek, $yesterday)) {
                $groupedSessions['This Week'][] = $session;
            } elseif ($lastActive->between($startOfLastWeek, $endOfLastWeek)) {
                $groupedSessions['Last Week'][] = $session;
            } elseif ($lastActive->between($startOfThisMonth, $now)) {
                $groupedSessions['This Month'][] = $session;
            } elseif ($lastActive->between($startOfLastMonth, $endOfLastMonth)) {
                $groupedSessions['Last Month'][] = $session;
            } else {
                $groupedSessions['Older'][] = $session;
            }
        }

        return view('chatbot.chatbot', [
            'chatbot_session_id' => $chatbot_session_id,
            'groupedSessions' => $groupedSessions
        ]);
    }


    public function loadMoreChats(Request $request)
    {
        $page = (int) $request->get('page', 1);
        $perPage = 10;
        $chatHistory = [];
        $totalMessages = 0;

        $sessionId = session('chatbot_session_id');

        if (!$sessionId) {
            return response()->json([
                'html' => '',
                'hasMore' => false,
                'error' => 'No session found'
            ]);
        }

        $record = DB::table('chatbot_history')
            ->where('session', $sessionId)
            ->first();

        if ($record && $record->json_data) {
            $allMessages = array_reverse(json_decode($record->json_data, true) ?? []);
            $totalMessages = count($allMessages);
            $offset = ($page - 1) * $perPage;
            $chatHistory = array_slice($allMessages, $offset, $perPage);
        }

        $user = Auth::user();
        $userAvatar = $user && check_file($user->avatar)
            ? get_file($user->avatar)
            : asset('default-avatar.png');

        $html = '';

        foreach ($chatHistory as $entry) {
            $question = htmlspecialchars($entry['question'] ?? '');
            $answer = $entry['answer'] ?? '';

            if (preg_match('/^\s*[\d\-\*\.]/m', $answer)) {
                // Convert to list format
                $lines = array_filter(array_map('trim', explode("\n", $answer)));
                $listItems = '';

                foreach ($lines as $line) {
                    $content = preg_replace('/^\d+\.?\s*|\-\s*|\*\s*/', '', $line);
                    if ($content) {
                        $listItems .= '<li>' . htmlspecialchars($content) . '</li>';
                    }
                }

                $answer = $listItems ? '<ul>' . $listItems . '</ul>' : '';
            } else {
                $answer = nl2br(htmlspecialchars($answer));
            }


            $html .= '
                <div class="chat-message bot-message" style="display: flex; align-items: flex-start; margin-bottom: 20px;">
                    <div class="bot-avatar" style="margin-right: 10px;"><i class="fas fa-robot"></i></div>
                    <div class="message bot">' . $answer . '</div>
                     <button class="message-icon copy-btn" title="Copy" data-message="' . e($answer) . '"><i class="fas fa-copy"></i></button>
                </div>
                
                <div class="chat-message user-message" style="display: flex; justify-content: flex-end; align-items: flex-end; flex-direction: column; margin-bottom: 10px;">
                    <div style="display: flex; justify-content: flex-end; align-items: flex-end; width: 100%;">
                        <div class="user-bubble" style="background-color: #0084ff; color: white; padding: 10px 15px; border-radius: 18px; max-width: 75%; margin-right: 10px;">'
                . $question .
                '</div>
                        <div class="user-avatar" style="width: 35px; height: 35px; border-radius: 50%; overflow: hidden;">
                            <img src="' . $userAvatar . '" alt="User" style="width: 100%; height: 100%; border-radius: 50%;" />
                        </div>
                    </div>
                    <div class="message-actions" style="margin-top: 4px; margin-right: 55px; align-self: flex-end;">
                        <button class="message-icon edit-btn" title="Edit" data-message="' . e($question) . '"><i class="fas fa-edit"></i></button>
                        <button class="message-icon copy-btn" title="Copy" data-message="' . e($question) . '"><i class="fas fa-copy"></i></button>
                    </div>
                </div>';
        }

        return response()->json([
            'html' => $html,
            'hasMore' => ($page * $perPage) < $totalMessages
        ]);
    }


    public function uploadAndTrainFAQ(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'faq_excel' => 'required|file|mimes:xlsx,xls',
            ]);

            try {
                $file = $request->file('faq_excel');

                // Store file in public/uploads/chatbot
                $uploadPath = public_path('uploads/chatbot');
                if (!File::exists($uploadPath)) {
                    File::makeDirectory($uploadPath, 0755, true);
                }

                $originalFilename = time() . '_' . $file->getClientOriginalName();
                $file->move($uploadPath, $originalFilename);

                // Load and read Excel
                $spreadsheet = IOFactory::load($uploadPath . '/' . $originalFilename);
                $sheet = $spreadsheet->getActiveSheet();
                $rows = $sheet->toArray();

                $questions = [];
                $answers = [];

                foreach ($rows as $index => $row) {
                    if ($index === 0)
                        continue;
                    $question = trim($row[0] ?? '');
                    $answer = trim($row[1] ?? '');

                    if ($question && $answer) {
                        $questions[] = $question;
                        $answers[] = $answer;
                    }
                }

                if (empty($questions)) {
                    $redirectUrl = route('chatbot');
                    $message = 'No valid questions found in the file.';
                    return response()->json([
                        'status' => false,
                        'response_code' => 300,
                        'message' => $message,
                        'data' => ['redirect_url' => $redirectUrl]
                    ], 300);
                }

                // Get embeddings from OpenAI
                $response = Http::timeout(15)->retry(3, 200)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                    ])->post('https://api.openai.com/v1/embeddings', [
                        'model' => 'text-embedding-3-small',
                        'input' => $questions,
                    ]);

                if (!$response->successful()) {
                    $redirectUrl = route('chatbot');
                    $message = 'Failed to connect to OpenAI API.';
                    return response()->json([
                        'status' => false,
                        'response_code' => 300,
                        'message' => $message,
                        'data' => ['redirect_url' => $redirectUrl]
                    ], 300);
                }

                $embeddings = $response['data'];
                $faqs = [];

                foreach ($questions as $i => $question) {
                    $faqs[] = [
                        'question' => $question,
                        'answer' => $answers[$i],
                        'embedding' => $embeddings[$i]['embedding'],
                    ];
                }

                // Save vector data as JSON in public/uploads/chatbot
                $jsonPath = $uploadPath . '/faqs_vectors.json';
                $saved = file_put_contents($jsonPath, json_encode($faqs, JSON_PRETTY_PRINT));

                if (!$saved) {
                    $redirectUrl = route('chatbot');
                    $message = 'FAQ trained but failed to save vectors.';
                    return response()->json([
                        'status' => false,
                        'response_code' => 300,
                        'message' => $message,
                        'data' => ['redirect_url' => $redirectUrl]
                    ], 300);
                }

                $redirectUrl = route('chatbot');
                $message = 'FAQ data trained and saved successfully!';
                return response()->json([
                    'status' => true,
                    'response_code' => 200,
                    'message' => $message,
                    'data' => ['redirect_url' => $redirectUrl]
                ], 200);
            } catch (\Exception $e) {
                $redirectUrl = route('chatbot');
                $message = 'Training failed: ' . $e->getMessage();
                return response()->json([
                    'status' => false,
                    'response_code' => 300,
                    'message' => $message,
                    'data' => ['redirect_url' => $redirectUrl]
                ], 300);
            }
        }

        return view('chatbot.uploadAndTrainFAQ');
    }

    public function send(Request $request)
    {
        $message = trim($request->input('message'));
        $mentions = $request->input('mentions');
        $faqPath = public_path('uploads/chatbot/faqs_vectors.json');
        $nlpPath = public_path('uploads/chatbot/nlp_tasks.json');
        $sessionId = session('chatbot_session_id');
        $faqData = [];
        $bestScore = null;
        $source = 'chatgpt';

        $mentionMap = collect($mentions)->mapWithKeys(fn($m) => [$m['name'] => $m['email']])->toArray();
        $emailToNameMap = collect($mentions)->mapWithKeys(fn($m) => [$m['email'] => $m['name']])->toArray();

        foreach ($mentionMap as $name => $email) {
            $message = str_replace('@' . $name, $email, $message);
        }

        // ------------------ Classify Question Type ------------------

        $questionType = 'general';
        $typeResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
        ])->timeout(30)->retry(2, 2000)->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4',
            'messages' => [
                ['role' => 'system', 'content' => <<<EOT
                You are a classifier for a chatbot that supports only four types of queries:
                - "faq": When the user asks a known question from the FAQ dataset.
                - "task": When the user asks something related to our internal task management system (e.g. tasks, ETC, ATC, deadlines, assignments, user activities, or mentions).
                - "general": All other casual or general-purpose questions not related to FAQs or tasks.
                - "product": All questions or orders related to any products that are being sold on our website 5core.com.
                
                Only respond with one word : "faq", "task", "general" or "product".
                EOT],
                ['role' => 'user', 'content' => $message],
            ],
            'temperature' => 0,
        ]);

        if ($typeResponse->successful()) {
            $classification = trim(strtolower($typeResponse['choices'][0]['message']['content']));
            if (in_array($classification, ['faq', 'task', 'general', 'product'])) {
                $questionType = $classification;
            }
        }

        // Manual keyword check to override misclassified task types
        $taskKeywords = ['task', 'deadline', 'assign', 'mention', 'status', 'update', 'complete'];
        $isLikelyTaskRelated = collect($taskKeywords)->contains(fn($kw) => str_contains(strtolower($message), $kw));
        if ($questionType === 'task' && !$isLikelyTaskRelated) {
            $questionType = 'general';
        }

        // ------------------ Handle FAQ ------------------

        if ($questionType === 'faq') {
            $faqData = file_exists($faqPath) ? json_decode(file_get_contents($faqPath), true) : [];
            if (!empty($faqData)) {
                $embedResponse = Http::withHeaders([
                    'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                ])->timeout(120)->retry(3, 5000)->post('https://api.openai.com/v1/embeddings', [
                    'model' => 'text-embedding-3-small',
                    'input' => $message,
                ]);

                if (!$embedResponse->successful()) {
                    return response()->json(['reply' => "I couldn't process your question properly. Please try again."], 500);
                }

                $userVector = $embedResponse['data'][0]['embedding'];
                $bestMatch = null;
                $bestScore = -1;

                foreach ($faqData as $faq) {
                    $score = $this->cosineSimilarity($userVector, $faq['embedding']);
                    if ($score > $bestScore) {
                        $bestScore = $score;
                        $bestMatch = $faq;
                    }
                }

                if ($bestScore >= 0.80) {
                    $reply = $bestMatch['answer'];
                    $source = 'faq';
                    goto respond;
                }
            }
        }

        // ------------------ Handle Task ------------------

        if ($questionType === 'task') {
            $mentionMapJson = json_encode($mentionMap);
            $nlpPrompt = $this->nlpPrompt($message, $mentionMapJson);
            $nlpCache = file_exists($nlpPath) ? json_decode(file_get_contents($nlpPath), true) : [];

            $nlpResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            ])->timeout(120)->retry(3, 5000)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a smart NLP-to-SQL generator.'],
                    ['role' => 'user', 'content' => $nlpPrompt],
                ],
                'temperature' => 0.2,
            ]);

            if ($nlpResponse->successful()) {
                $nlp = json_decode($nlpResponse['choices'][0]['message']['content'], true);
                $resultArray = [];

                if (!empty($nlp['sql_query'])) {
                    $sqlQueries = is_array($nlp['sql_query']) ? $nlp['sql_query'] : [$nlp['sql_query']];
                    foreach ($sqlQueries as $query) {
                        $partial = DB::select($query);
                        $resultArray[] = json_decode(json_encode($partial), true);
                    }
                }

                $replyPrompt = $this->replyPrompt($nlp, $resultArray, $emailToNameMap);

                $replyResponse = Http::withHeaders([
                    'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                ])->timeout(120)->retry(3, 5000)->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a friendly chatbot that converts SQL results into human-readable responses.'],
                        ['role' => 'user', 'content' => $replyPrompt],
                    ],
                    'temperature' => 0.5,
                ]);

                $reply = $replyResponse->successful() ? $replyResponse['choices'][0]['message']['content'] : "I tried analyzing your Question, but couldn't generate a proper response.";
                $source = 'nlp-ai';

                // Cache task if new
                $questionExists = collect($nlpCache)->contains(fn($entry) => strtolower(trim($entry['question'])) === strtolower(trim($message)));

                if (!$questionExists) {
                    $nlpCache[] = [
                        'question' => $message,
                        'intent' => $nlp['intent'] ?? null,
                        'entities' => $nlp['entities'] ?? null,
                        'sql_query' => $nlp['sql_query'] ?? null,
                        'created_at' => now()->toDateTimeString()
                    ];
                    file_put_contents($nlpPath, json_encode($nlpCache, JSON_PRETTY_PRINT));
                }

                goto respond;
            }
        }

        // ------------------ Handle General ------------------

        if ($questionType === 'general') {
            $generalResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            ])->timeout(60)->retry(2, 3000)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                    ['role' => 'user', 'content' => $message],
                ],
                'temperature' => 0.7,
            ]);

            $reply = $generalResponse->successful()
                ? $generalResponse['choices'][0]['message']['content']
                : "I'm sorry, I couldn't generate a response to your question.";
            $source = 'chatgpt';

            goto respond;
        }

        if ($questionType === 'product') {



            $reply = $this->handleProductQuery($message)
                ?? "I'm sorry, I couldn't generate a response to your question.";
            $source = 'chatgpt';

            goto respond;
        }


        // ------------------ Respond ------------------

        respond:

        $title = null;
        $topic = null;

        $analysisResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
        ])->timeout(60)->retry(2, 2000)->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4',
            'messages' => [
                ['role' => 'system', 'content' => 'You extract metadata from questions. Return JSON with "title" and "topic".'],
                ['role' => 'user', 'content' => "Question: \"$message\"\nRespond in JSON."],
            ],
            'temperature' => 0.5,
        ]);

        if ($analysisResponse->successful()) {
            $analysisContent = $analysisResponse['choices'][0]['message']['content'] ?? null;
            $analysisJson = is_string($analysisContent) ? json_decode($analysisContent, true) : null;

            if (is_array($analysisJson)) {
                $title = $analysisJson['title'] ?? null;
                $topic = $analysisJson['topic'] ?? null;
            } else {
                Log::warning('Failed to decode analysis JSON', ['content' => $analysisContent]);
            }
        }

        $score = ($source === 'faq' && isset($bestScore)) ? $bestScore : null;
        $storedQuestion = $this->replaceEmailsWithNames($message, $emailToNameMap);
        $chatRecord = DB::table('chatbot_history')->where('session', $sessionId)->first();
        $chatData = [
            [
                'question' => $storedQuestion,
                'answer' => $reply,
                'source' => $source,
                'timestamp' => now()->toDateTimeString(),
                'score' => $score,
                'title' => $title,
                'topic' => $topic
            ]
        ];

        if ($chatRecord) {
            $existingData = json_decode($chatRecord->json_data, true);
            $chatData = array_merge($existingData, $chatData);
            DB::table('chatbot_history')->where('id', $chatRecord->id)->update([
                'json_data' => json_encode($chatData),
                'updated_at' => now()
            ]);
        } else {
            DB::table('chatbot_history')->insert([
                'user_id' => auth()->id(),
                'session' => $sessionId,
                'json_data' => json_encode($chatData),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return response()->json(['reply' => $reply]);
    }

    function fetchShopifyProducts(): array
    {
        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => env('SHOPIFY_ACCESS_TOKEN'),
        ])->get("https://5core.com/admin/api/2025-07/products.json");

        return $response->json('products');
    }


    function formatProducts(array $products): array
    {
        return [
            [
                'title' => "5 Core Speakers",
                'price' => "USD 500",
                'description' => "100W Speaker",
            ],
            [
                'title' => "5 Core Extra Bass Speakers",
                'price' => "USD 750",
                'description' => "200W Speaker",
            ],
        ];

        // return collect($products)->map(function ($product) {
        //     return [
        //         'title' => $product['title'],
        //         'price' => $product['variants'][0]['price'] ?? null,
        //         'description' => strip_tags($product['body_html']),
        //         'image' => $product['image']['src'] ?? null,
        //     ];
        // })->toArray();
    }

    function askChatGPT(string $question, array $products = null): string
    {
        $apiKey = env('OPENAI_API_KEY');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
        ])->timeout(30)->retry(2, 2000)->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a intelligent shopping assistant. Use the product data below to suggest items.',
                ],
                [
                    'role' => 'user',
                    'content' => $question . "\n\nProducts:\n" . json_encode($products, JSON_PRETTY_PRINT),
                ],
            ],
            'temperature' => 0,
        ]);


        return $response->json('choices')[0]['message']['content'] ?? 'No response.';
    }

    function queryShopify(string $graphqlQuery): array
    {

        $accessToken = env('SHOPIFY_ACCESS_TOKEN'); // Private App Token or Storefront Token

        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $accessToken,
            'Content-Type' => 'application/json',
        ])->post("https://5-core.myshopify.com/admin/api/2025-07/graphql.json", [
            'query' => $graphqlQuery,
        ]);

        if ($response->failed()) {
            throw new \Exception('Shopify query failed: ' . $response->body());
        }

        return $response->json('data') ?? [];
    }



    public function handleProductQuery($msg)
    {
        $msg = $string = str_replace(array("\n", "\r\n", "\r"), '', $msg);

        $requiredGraphQL = GraphQLGenerator::fromQuestion($msg);

        $shopifyResponse = $this->queryShopify($requiredGraphQL)["products"] ?? null;

        $answer = $this->askChatGPT($msg, $shopifyResponse);

        return $answer;
    }




    private function nlpPrompt($message, $mentionMapJson)
    {
        return <<<EOT
You are converting a user message into SQL. Here is the schema of the 'tasks' table:

- id / task_id: Unique IDs
- title: Task title
- description: Task details
- group: Type or category of task
- workspace, order
- priority: Low, Medium, High
- status: Todo, Not Started, Working, In Progress, Monitor, Done, Need Help, Review, Need Approval, Dependent, Approved, Hold, Rework, Urgent, Q-Task, Not Applicable, etc.
- `eta_time`: [ETC: Estimated time to complete], stored in minutes (e.g., "60", "20")
- `etc_done`: [ATC: Actual time to complete], stored in minutes (e.g., "60", "20")
- start_date, due_date, completion_date
- deleted_at: NULL = active or pending, NOT NULL = deleted
- assignor, assign_to: email addresses
- created_at: task assign date 

Message: "{$message}"

Mentions (map): {$mentionMapJson}

Important rules:
- For "due" tasks: Include only if `due_date` <= CURRENT_DATE (including today) AND `deleted_at` IS NULL.
- If user asks for "done", "completed", or "finished" tasks AND also mentions they are "deleted" (e.g., "deleted done tasks", "completed but deleted", "finished and removed"):
   - Then generate SQL WHERE clause as:
     `status` = 'Done' AND `deleted_at` IS NOT NULL

- If user asks for "done", "completed", or "finished" tasks but does NOT say anything about deletion:
   - Then generate WHERE clause as:
     `status` = 'Done'
   - Do NOT include any condition on `deleted_at`

- For "pending" tasks: Include only if the user specifically mentions "pending", "incomplete", or "not done". Then apply: `status` != 'Done' AND `deleted_at` IS NULL.

- If the user asks for both a list and a count: Return two SQL queries—one to SELECT * FROM `tasks`; and one to SELECT COUNT(*).

- Always enclose column and table names in backticks (e.g., `title`, `due_date`) in all SQL queries.

- If no task status is mentioned: Do not apply any status-based filters.

- Only apply `assign_to` or `assignor` filters if a matching name/email exists in the provided mapping. Ignore generic terms like "employee", "staff", etc., unless they are mapped.

- Ensure each SQL query is syntactically valid and can run successfully in MySQL.

- For time-based questions related to `eta_time` or `etc_done`:
  - The stored value is in minutes.
  - If the user asks for time in hours, convert using: minutes / 60.
  - If the user asks for seconds, convert using: minutes * 60.
  - If the user asks for minutes, return as-is.
  - If no unit is mentioned, return the result in minutes by default.

Respond only in JSON: { intent, entities, sql_query }.
If more than one query is needed (e.g. list + count), return a single JSON field "sql_query" as an array of SQL statements.
EOT;
    }


    private function replyPrompt($nlp, $resultArray, $emailToNameMap)
    {
        $intent = is_array($nlp) && isset($nlp['intent']) ? $nlp['intent'] : 'unknown';
        $entities = is_array($nlp) && isset($nlp['entities']) ? $nlp['entities'] : [];

        // Safely encode inputs
        $intentJson = json_encode($intent, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $entitiesJson = json_encode($entities, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $resultJson = json_encode($resultArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $emailToNameJson = json_encode($emailToNameMap, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return "You are a helpful assistant. Given the following:
            
            - Intent: $intentJson
            - Entities: $entitiesJson
            - SQL result: $resultJson
            - Email to Name Mapping: $emailToNameJson
            
            Instructions:
            1. Format the response with appropriate emojis for each part:
               - Main response: 🎯 
               - Lists:  ➡️ for each bullet point
               - Counts/summaries: 📊 
               - Tasks: 
                 - Overdue: ⚠️ 
                 - Completed: ✅ 
                 - Pending: ⌛ 
                 - High priority: 🔴 
                 - Medium priority: 🟡 
                 - Low priority: 🟢 
               - Dates/deadlines: 📅 
               - Assignments: 👤 
               - Questions: ❓ 
               - Important notes: 💡 
            
            2. Structure guidelines:
               - Start with 🎯 for the main response
               - Use  ➡️ for list items
               - Include 📊 for counts/summaries
               - Highlight important info with relevant emojis
               - Keep professional but friendly tone
            
            3. Specific formatting:
               - For task lists: 
                  ➡️ [Task Title] (Group)
               - For counts: 
                 📊 Total: [number] tasks found
               - For mixed requests: 
                 🎯 Here are the details:
                  ➣ [Item 1]
                  ➣ [Item 2]
                 📊 Summary: [count]
            
            Make the reply professional but warm.";
    }

    private function replaceEmailsWithNames($message, $emailToNameMap)
    {
        foreach ($emailToNameMap as $email => $name) {
            $message = str_replace($email, '@' . $name, $message);
        }
        return $message;
    }

    private function formatReply($intent, $entities, $result)
    {
        $count = $result[0]['COUNT(*)'] ?? 0;

        return match ($intent) {
            'count_tasks_by_assignor_to_assignee' =>
            $count == 0
                ? "There are no tasks assigned by {$entities['assignor_name']} to {$entities['assign_to_name']}."
                : "There are {$count} tasks assigned by {$entities['assignor_name']} to {$entities['assign_to_name']}.",

            'count_tasks_by_status' =>
            "There are {$count} tasks with status '{$entities['status']}'",

            'count_tasks_by_priority_and_status' =>
            "There are {$count} {$entities['priority']} priority tasks currently marked as '{$entities['status']}'",

            default => "Result count: {$count}."
        };
    }

    private function cosineSimilarity($vec1, $vec2)
    {
        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        for ($i = 0; $i < count($vec1); $i++) {
            $dot += $vec1[$i] * $vec2[$i];
            $normA += $vec1[$i] ** 2;
            $normB += $vec2[$i] ** 2;
        }

        return $normA && $normB ? $dot / (sqrt($normA) * sqrt($normB)) : 0;
    }


    public function userList()
    {
        $users = User::select('id', 'name', 'email', 'avatar')->get();

        $defaultAvatar = asset('default-avatar.png');

        $users = $users->map(function ($user) use ($defaultAvatar) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                // Use your own helper or Laravel's asset() function here:
                'avatar' => check_file($user->avatar) ? get_file($user->avatar) : $defaultAvatar
            ];
        });

        return response()->json($users);
    }
}
