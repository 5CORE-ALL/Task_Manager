<?php

namespace App\Helpers;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Http;

class GraphQLGenerator
{
    public static function fromQuestion(string $question): string
    {
        $prompt = <<<EOT
You are an expert in Shopify GraphQL API.

Your task is to convert a customer product-related question into an accurate Shopify GraphQL query.
Use only the fields listed in the schema below. Do not invent fields or rely on tags unless explicitly asked.

Assume the following product schema:
- title
- productType
- vendor
- tags
- variants { price, sku, inventoryQuantity }
- metafields(namespace: "ws_genius", key: "custom_prices")
- metafields(namespace: "yoast_seo", key: "indexable")
- metafields(namespace: "global", key: "title_tag")

Guidelines:
- If the question asks for "best" products, prefer items with high inventory and lowest price.
- If the question includes a price limit (e.g., "under 100 USD"), apply that to the **variant price**.
- Only use tags if the question explicitly mentions them (e.g., "products tagged with 'sale'").
- Ignore any irrelevant data. Use filtering logic only with available fields.

Question: "{$question}"
EOT;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
        ])->timeout(30)->retry(2, 2000)->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a Shopify GraphQL expert.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.2,
        ]);

        $content = $response['choices'][0]['message']['content'];

        // Extract GraphQL code block (remove ```graphql ``` markers if present)
        if (preg_match('/```graphql(.*?)```/s', $content, $matches)) {
            return trim($matches[1]);
        }

        return trim($content); // fallback
    }
}
