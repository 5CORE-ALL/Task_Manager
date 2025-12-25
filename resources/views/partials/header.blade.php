<header
    class="dash-header {{ empty($company_settings['site_transparent']) || $company_settings['site_transparent'] == 'on' ? 'transprent-bg' : '' }} ">
    <div class="header-wrapper">
        <div class="me-auto dash-mob-drp">
            <ul class="list-unstyled">
                <li class="dash-h-item mob-hamburger">
                    <a href="#!" class="dash-head-link" id="mobile-collapse">
                        <div class="hamburger hamburger--arrowturn">
                            <div class="hamburger-box">
                                <div class="hamburger-inner"></div>
                            </div>
                        </div>
                    </a>
                </li>

                <li class="dropdown dash-h-item drp-company">
                    <a class="dash-head-link dropdown-toggle arrow-none m-0" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false"aria-expanded="false">
                        @if (!empty(Auth::user()->avatar))
                            <span class="theme-avtar">
                                <img alt="#"
                                    src="{{ check_file(Auth::user()->avatar) ? get_file(Auth::user()->avatar) : '' }}"
                                    class="rounded border-2 border border-primary" style="width: 100% ; height: 100%">
                            </span>
                        @else
                            <span class="theme-avtar">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        @endif
                        <span class="hide-mob ms-2">{{ Auth::user()->name }}</span>
                        <i class="ti ti-chevron-down drp-arrow nocolor hide-mob"></i>
                    </a>
                    <div class="dropdown-menu dash-h-dropdown">
                        @permission('user profile manage')
                            <a href="{{ route('profile') }}" class="dropdown-item">
                                <i class="ti ti-user"></i>
                                <span>{{ __('Profile') }}</span>
                            </a>
                        @endpermission
                        <a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('frm-logout').submit();"
                            class="dropdown-item">
                            <i class="ti ti-power"></i>
                            <span>{{ __('Logout') }}</span>
                        </a>
                        <form id="frm-logout" action="{{ route('logout') }}" method="POST" class="d-none">
                            {{ csrf_field() }}
                        </form>
                    </div>
                </li>
                                <!-- Incentive Amount Display -->
                <li class="dash-h-item" id="incentiveAmountDisplay" style="display: none;">
                    <a class="dash-head-link me-0" href="#" style="background: #28a745; color: white;">
                        <i class="ti ti-gift"></i>
                        <span class="hide-mob">Incentive: â‚¹<span id="incentiveAmount">0.00</span></span>
                    </a>
                </li>
                
                <!-- Timer add  -->
                <li class="dash-h-item">
                    <a class="dash-head-link me-0" href="#" data-bs-toggle="modal" data-bs-target="#worldClockModal">
                        <i class="ti ti-clock" style="font-size: 22px;"></i>
                        <span class="hide-mob" style="margin-left: 6px; font-weight: 600;">Clocks</span>
                    </a>
                </li>
            <!-- World Clock Modal -->
            <div class="modal fade" id="worldClockModal" tabindex="-1" aria-labelledby="worldClockModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <div class="modal-header" style="background: linear-gradient(90deg, #2196f3 0%, #00b8ff 100%);">
                            <h5 class="modal-title text-white" id="worldClockModalLabel">
                                <i class="ti ti-clock" style="font-size: 24px; vertical-align: middle; margin-right: 8px;"></i>
                                World Clocks
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" style="background: #f7f7f7;">
                            <div class="row text-center" style="gap: 18px; justify-content: center;">
                                <div class="col-5 p-3 rounded shadow-sm" style="background: #fff;">
                                    <div style="font-size: 18px; font-weight: 600; color: #2196f3;">India (IN)</div>
                                    <div id="indian-clock-modal" style="font-size: 1.5rem; font-weight: bold;"></div>
                                </div>
                                <div class="col-5 p-3 rounded shadow-sm" style="background: #fff;">
                                    <div style="font-size: 18px; font-weight: 600; color: #ff9800;">California (CA)</div>
                                    <div id="california-clock-modal" style="font-size: 1.5rem; font-weight: bold;"></div>
                                </div>
                                <div class="col-5 p-3 rounded shadow-sm" style="background: #fff;">
                                    <div style="font-size: 18px; font-weight: 600; color: #00c853;">Ohio</div>
                                    <div id="ohio-clock-modal" style="font-size: 1.5rem; font-weight: bold;"></div>
                                </div>
                                <div class="col-5 p-3 rounded shadow-sm" style="background: #fff;">
                                    <div style="font-size: 18px; font-weight: 600; color: #d32f2f;">China</div>
                                    <div id="china-clock-modal" style="font-size: 1.5rem; font-weight: bold;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

<script>
function updateWorldClocks() {
    // India: Asia/Kolkata
    let india = new Date().toLocaleString('en-US', { timeZone: 'Asia/Kolkata', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false });
    // California: America/Los_Angeles
    let california = new Date().toLocaleString('en-US', { timeZone: 'America/Los_Angeles', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false });
    // Ohio: America/New_York
    let ohio = new Date().toLocaleString('en-US', { timeZone: 'America/New_York', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false });
    // China: Asia/Shanghai
    let china = new Date().toLocaleString('en-US', { timeZone: 'Asia/Shanghai', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false });
    document.getElementById('indian-clock-modal').textContent = india;
    document.getElementById('california-clock-modal').textContent = california;
    document.getElementById('ohio-clock-modal').textContent = ohio;
    document.getElementById('china-clock-modal').textContent = china;
}
document.addEventListener('DOMContentLoaded', function() {
    updateWorldClocks();
    setInterval(updateWorldClocks, 1000);
    // Also update when modal is shown
    $('#worldClockModal').on('show.bs.modal', function() {
        updateWorldClocks();
    });
});
</script>

<style>
#worldClockModal .modal-content {
    border-radius: 1rem;
    box-shadow: 0 8px 32px rgba(0,0,0,0.18);
    border: none;
    overflow: hidden;
}
#worldClockModal .modal-header {
    border-bottom: none;
    border-radius: 1rem 1rem 0 0;
}
#worldClockModal .modal-body {
    padding: 2rem 1rem;
}
#worldClockModal .row > .col-5 {
    margin-bottom: 12px;
    min-width: 180px;
}
</style>

            </ul>
        </div>
        <div class="ms-auto">
            <ul class="list-unstyled">
                @impersonating($guard = null)
                    <li class="dropdown dash-h-item drp-company">
                        <a class="btn btn-danger btn-sm me-3" href="{{ route('exit.company') }}"><i class="ti ti-ban"></i>
                            {{ __('Exit Company Login') }}
                        </a>
                    </li>
                @endImpersonating
                 <!-- Operations Dropdown (NEW) -->

                                <!-- Announcement Dropdown (NEW) -->
                
                <!-- ADDED: Incentive Dropdown (only for specific email addresses) -->
                @if(in_array(Auth::user()->email, ['president@5core.com', 'tech-support@5core.com','inventory@5core.com']))
              
                @endif
                <!-- Review Button -->
                <!--<li class="dash-h-item">-->
                <!--    <a class="dash-head-link me-0" href="#" data-bs-toggle="modal" data-bs-target="#reviewModal" title="Submit Review" style="background: #ffd000;">-->
                <!--        <i class="ti ti-star" style="font-size: 20px; color: #111; vertical-align: middle;"></i>-->
                <!--        <span class="hide-mob">Improvements</span> -->
                <!--    </a>-->
                <!--    <a href="https://www.5core.com/pages/improvements" target="_blank" title="Info"-->
                <!--       style="display:inline-block; margin-left:6px; vertical-align:middle;">-->
                <!--        <i class="ti ti-info-circle" style="font-size: 18px; color:#00b8ff;"></i>-->
                <!--    </a>-->
                    
                <!--</li>-->


<!-- FLAG RAISE MODAL - REMOVED: Flags are now created via task creation -->


                @permission('user chat manage')
                                <!-- Apps displayed directly in header -->
                                <li class="dash-h-item position-relative" style="z-index:1052;">
                                    <div class="apps-toolbar">
                                        <!-- ChatGPT -->
                                        <a class="header-app-item" href="https://chat.openai.com/" target="_blank" rel="noopener noreferrer" title="ChatGPT">
                                            <img src="https://upload.wikimedia.org/wikipedia/commons/0/04/ChatGPT_logo.svg" alt="ChatGPT" class="header-app-icon" />
                                        </a>
                                        <!-- Chatbot -->
                                        <a class="header-app-item" href="{{ route('chatbot') }}" target="_blank" rel="noopener noreferrer" title="Chatbot">
                                            <img src="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/svgs/solid/robot.svg" alt="Chatbot" class="header-app-icon" />
                                        </a>
                                        <!-- DAR -->
                                        <a class="header-app-item" href="#" data-bs-toggle="modal" data-bs-target="#darModal" title="Daily Activity Report">
                                            <img src="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/svgs/regular/clock.svg" alt="DAR" class="header-app-icon" />
                                        </a>
                                        <!-- Flags -->
                                        <a class="header-app-item" href="{{ route('flag-raise.history') }}" target="_blank" rel="noopener noreferrer" title="Show Flag History">
                                            <img src="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/svgs/regular/flag.svg" alt="Flags" class="header-app-icon" />
                                        </a>
                                        <!-- Improvements with submenu -->
                                        <div class="header-app-item position-relative" id="improvementsItem" title="Improvements">
                                            <img src="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/svgs/solid/wrench.svg" alt="Improvements" class="header-app-icon" />
                                            <div class="submenu position-absolute shadow bg-white rounded py-2" id="improvementsMenu" style="display:none; top: 100%; left: 0; z-index: 3000; min-width: 140px; margin-top: 8px;">
                                                <div class="px-3 py-1 submenu-item">
                                                    <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#reviewModal">
                                                        <i class="ti ti-plus"></i>
                                                        <span>Create Imp</span>
                                                    </a>
                                                </div>
                                                <div class="px-3 py-1 submenu-item">
                                                    <a href="{{ route('reviews.index') }}" target="_blank" class="dropdown-item">
                                                        <i class="ti ti-list"></i>
                                                        <span>Show Imp</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Achieve -->
                                        <a class="header-app-item" href="https://accomplish.5coremanagement.com/" target="_blank" rel="noopener noreferrer" title="Achieve">
                                            <img src="https://cdn.jsdelivr.net/gh/twitter/twemoji@14.0.2/assets/svg/2705.svg" alt="Achieve" class="header-app-icon" />
                                        </a>
                                        <!-- Operations with submenu -->
                                        <div class="header-app-item position-relative" id="operationsItem" title="Operations">
                                            <img src="https://cdn-icons-png.flaticon.com/512/1046/1046870.png" alt="Operations Warehouse" class="header-app-icon" />
                                            <div class="submenu position-absolute shadow bg-white rounded py-2" id="operationsMenu" style="display:none; top: 100%; left: 0; z-index: 3000; min-width: 140px; margin-top: 8px;">
                                                <div class="px-3 py-1 submenu-item">
                                                    <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#dailyShippingChecklistModal">
                                                        <i class="ti ti-truck"></i>
                                                        <span>Daily Checklist</span>
                                                    </a>
                                                </div>
                                                <div class="px-3 py-1 submenu-item">
                                                    <a href="{{ route('shipping-checklist.index') }}" class="dropdown-item" target="_blank">
                                                        <i class="ti ti-list"></i>
                                                        <span>Show Checklist</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Incentive with submenu -->
                                        <div class="header-app-item position-relative" id="incentiveItem" title="Incentive">
                                            <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Incentive" class="header-app-icon" />
                                            <div class="submenu position-absolute shadow bg-white rounded py-2" id="incentiveMenu" style="display:none; top: 100%; left: 0; z-index: 3000; min-width: 140px; margin-top: 8px;">
                                                <div class="px-3 py-1 submenu-item">
                                                    <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#incentiveModal">
                                                        <i class="ti ti-plus"></i>
                                                        <span>Add Incentive</span>
                                                    </a>
                                                </div>
                                                <div class="px-3 py-1 submenu-item">
                                                    <a href="{{ route('incentives.index') }}" target="_blank" class="dropdown-item">
                                                        <i class="ti ti-list"></i>
                                                        <span>Show Incentives</span>
                                                    </a>
                                                </div>
                                                <div class="px-3 py-1 submenu-item">
                                                    <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#deductionModal">
                                                        <i class="ti ti-minus"></i>
                                                        <span>Add Deduction</span>
                                                    </a>
                                                </div>
                                                <div class="px-3 py-1 submenu-item">
                                                    <a href="{{ route('deductions.index') }}" target="_blank" class="dropdown-item">
                                                        <i class="ti ti-list"></i>
                                                        <span>Show Deductions</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Tutorial -->
                                        <a class="header-app-item" href="{{ route('tutorial') }}" target="_blank" rel="noopener noreferrer" title="Tutorial">
                                            <img src="https://cdn-icons-png.flaticon.com/512/0/375.png" alt="Tutorial" class="header-app-icon" />
                                        </a>
                                        <!-- Google Chat -->
                                        <a class="header-app-item" href="https://chat.google.com/" target="_blank" rel="noopener noreferrer" title="Chat">
                                            <img src="https://www.gstatic.com/images/branding/product/1x/chat_2020q4_48dp.png" alt="Chat" class="header-app-icon" />
                                        </a>
                                        <!-- Gmail -->
                                        <a class="header-app-item" id="gmailItem" href="https://mail.google.com/" target="_blank" rel="noopener noreferrer" title="Gmail">
                                            <img src="https://www.gstatic.com/images/branding/product/1x/gmail_2020q4_48dp.png" alt="Gmail" class="header-app-icon" />
                                        </a>
                                        <!-- Meet -->
                                        <a class="header-app-item" href="https://meet.google.com/landing" target="_blank" rel="noopener noreferrer" title="Meet">
                                            <img src="https://www.gstatic.com/images/branding/product/1x/meet_2020q4_48dp.png" alt="Meet" class="header-app-icon" />
                                        </a>
                                        <!-- Docs -->
                                        <a class="header-app-item" href="https://docs.google.com/" target="_blank" rel="noopener noreferrer" title="Docs">
                                            <img src="https://www.gstatic.com/images/branding/product/1x/docs_2020q4_48dp.png" alt="Docs" class="header-app-icon" />
                                        </a>
                                        <!-- Sheets -->
                                        <a class="header-app-item" href="https://sheets.google.com/" target="_blank" rel="noopener noreferrer" title="Sheets">
                                            <img src="https://www.gstatic.com/images/branding/product/1x/sheets_2020q4_48dp.png" alt="Sheets" class="header-app-icon" />
                                        </a>
                                        <!-- Calendar -->
                                        <a class="header-app-item" href="https://calendar.google.com/" target="_blank" rel="noopener noreferrer" title="Calendar">
                                            <img src="https://www.gstatic.com/images/branding/product/1x/calendar_2020q4_48dp.png" alt="Calendar" class="header-app-icon" />
                                        </a>
                                    </div>
                                </li>

                @endpermission
                 @permission('user chat manage')
                    @php
                        $unseenCounter = App\Models\ChMessage::where('to_id', Auth::user()->id)
                            ->where('seen', 0)
                            ->count();
                    @endphp
                    <li class="dash-h-item">
                       
                    </li>
                @endpermission
                @permission('workspace create')
                    <!--@if (PlanCheck('Workspace', Auth::user()->id) == true)-->
                    <!--    <li class="dash-h-item">-->
                    <!--        <a href="#!" class="dash-head-link dropdown-toggle arrow-none me-0 cust-btn"-->
                    <!--            data-url="{{ route('workspace.create') }}" data-ajax-popup="true" data-size="lg"-->
                    <!--            data-title="{{ __('Create New Workspace') }}">-->
                    <!--            <i class="ti ti-circle-plus"></i>-->
                    <!--            <span class="hide-mob">{{ __('Create Workspace') }}</span>-->
                    <!--        </a>-->
                    <!--    </li>-->
                    <!--@endif-->
                @endpermission
                @permission('workspace manage')
                    <!--<li class="dropdown dash-h-item drp-language">-->
                    <!--    <a class="dash-head-link dropdown-toggle arrow-none me-0 cust-btn" data-bs-toggle="dropdown"-->
                    <!--        href="#" role="button" aria-haspopup="false" aria-expanded="false"-->
                    <!--        data-bs-placement="bottom" data-bs-original-title="Select your bussiness">-->
                    <!--        <i class="ti ti-apps"></i>-->
                    <!--        <span class="hide-mob">{{ Auth::user()->ActiveWorkspaceName() }}</span>-->
                    <!--        <i class="ti ti-chevron-down drp-arrow nocolor"></i>-->
                    <!--    </a>-->
                    <!--    <div class="dropdown-menu dash-h-dropdown dropdown-menu-end" style="">-->
                    <!--        @foreach (getWorkspace() as $workspace)-->
                    <!--            @if ($workspace->id == getActiveWorkSpace())-->
                    <!--                <div class="d-flex justify-content-between bd-highlight">-->
                    <!--                    <a href=" # " class="dropdown-item ">-->
                    <!--                        <i class="ti ti-checks text-primary"></i>-->
                    <!--                        <span>{{ $workspace->name }}</span>-->
                    <!--                        @if ($workspace->created_by == Auth::user()->id)-->
                    <!--                            <span class="badge bg-dark">-->
                    <!--                                {{ Auth::user()->roles->first()->name }}</span>-->
                    <!--                        @else-->
                    <!--                            <span class="badge bg-dark"> {{ __('Shared') }}</span>-->
                    <!--                        @endif-->
                    <!--                    </a>-->
                    <!--                    @if ($workspace->created_by == Auth::user()->id)-->
                    <!--                        @permission('workspace edit')-->
                    <!--                            <div class="action-btn mt-2">-->
                    <!--                                <a data-url="{{ route('workspace.edit', $workspace->id) }}"-->
                    <!--                                    class="mx-3 btn" data-ajax-popup="true"-->
                    <!--                                    data-title="{{ __('Edit Workspace Name') }}" data-toggle="tooltip"-->
                    <!--                                    data-original-title="{{ __('Edit') }}">-->
                    <!--                                    <i class="ti ti-pencil text-success"></i>-->
                    <!--                                </a>-->
                    <!--                            </div>-->
                    <!--                        @endpermission-->
                    <!--                    @endif-->
                    <!--                </div>-->
                    <!--            @else-->
                    <!--            @php-->
                    <!--                $route = ($workspace->is_disable == 1) ?  route('workspace.change', $workspace->id) : '#';-->
                    <!--            @endphp-->
                    <!--                <div class="d-flex justify-content-between bd-highlight">-->

                    <!--                <a href="{{ $route }}" class="dropdown-item">-->
                    <!--                    <span>{{ $workspace->name }}</span>-->
                    <!--                    @if ($workspace->created_by == Auth::user()->id)-->
                    <!--                        <span class="badge bg-dark"> {{ Auth::user()->roles->first()->name }}</span>-->
                    <!--                    @else-->
                    <!--                        <span class="badge bg-dark"> {{ __('Shared') }}</span>-->
                    <!--                    @endif-->
                    <!--                </a>-->
                    <!--                @if ($workspace->is_disable == 0)-->
                    <!--                        <div class="action-btn mt-2">-->
                    <!--                            <i class="ti ti-lock"></i>-->
                    <!--                        </div>-->
                    <!--                    @endif-->
                    <!--                </div>-->
                    <!--            @endif-->
                    <!--        @endforeach-->
                    <!--        @if (getWorkspace()->count() > 1)-->
                    <!--            @permission('workspace delete')-->
                    <!--                <hr class="dropdown-divider" />-->
                    <!--                    <a href="#!" data-url="{{route('company.info', Auth::user()->id)}}" class="dropdown-item" data-ajax-popup="true" data-size="lg" data-title="{{__('Workspace Info')}}">-->
                    <!--                        <i class="ti ti-circle-x"></i>-->
                    <!--                        <span>{{ __('View') }}</span> <br>-->
                    <!--                    </a>-->


                    <!--                <hr class="dropdown-divider" />-->

                    <!--                <form id="remove-workspace-form"-->
                    <!--                    action="{{ route('workspace.destroy', getActiveWorkSpace()) }}" method="POST">-->
                    <!--                    @csrf-->
                    <!--                    @method('DELETE')-->
                    <!--                    <a href="#!" class="dropdown-item remove_workspace">-->
                    <!--                        <i class="ti ti-circle-x"></i>-->
                    <!--                        <span>{{ __('Remove') }}</span> <br>-->
                    <!--                        <small class="text-danger">{{ __('Active Workspace Will Consider') }}</small>-->
                    <!--                    </a>-->
                    <!--                </form>-->
                    <!--            @endpermission-->
                    <!--        @endif-->
                    <!--    </div>-->
                    <!--</li>-->
                @endpermission

                <!-- <li class="dropdown dash-h-item drp-language">
                    <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="ti ti-world nocolor"></i>
                        <span class="drp-text hide-mob">{{ Str::upper(getActiveLanguage()) }}</span>
                        <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                    </a>
                    <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">

                        @foreach (languages() as $key => $language)
                            <a href="{{ route('lang.change', $key) }}"
                                class="dropdown-item @if ($key == getActiveLanguage()) text-danger @endif">
                                <span>{{ Str::ucfirst($language) }}</span>
                            </a>
                        @endforeach
                        @if (Auth::user()->type == 'super admin')
                            @permission('language create')
                                <a href="#" data-url="{{ route('create.language') }}"
                                    class="dropdown-item border-top pt-3 text-primary" data-ajax-popup="true"
                                    data-title="{{ __('Create New Language') }}">
                                    <span>{{ __('Create Language') }}</span>
                                </a>
                            @endpermission
                            @permission('language manage')
                                <a href="{{ route('lang.index', [Auth::user()->lang]) }}"
                                    class="dropdown-item  pt-3 text-primary">
                                    <span>{{ __('Manage Languages') }}</span>
                                </a>
                            @endpermission
                        @endif
                    </div>
                </li> -->
            </ul>
                        <!-- Announcement Modal (NEW) -->
                         
<div class="modal fade" id="announcementModal" tabindex="-1" aria-labelledby="announcementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="announcementModalLabel">Create Announcement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="announcementForm">
                    @csrf
                    <!-- Announcement Group (input field) -->
                    <div class="mb-3">
                        <label for="announcement_group" class="form-label">Announcement Group</label>
                        <input type="text" class="form-control" id="announcement_group" name="announcement_group" placeholder="Enter group (e.g. HR, Tech, Sales, All)" required>
                    </div>
                    <!-- Select Team Member (searchable dropdown) -->
                    <div class="mb-3">
                        <label for="announcement_team_member" class="form-label">Select Team Member(s) <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <button type="button" class="btn btn-sm btn-outline-primary mb-2" id="selectAllAnnouncementMembers" style="position:absolute; right:0; top:-32px; z-index:2;">All Team Members</button>
                            <input type="text" class="form-control" id="announcement_team_member" name="announcement_team_member" placeholder="Type employee name..." autocomplete="off">
                            <input type="hidden" id="announcement_team_member_ids" name="announcement_team_member_ids" required>
                            <div id="announcementEmployeeDropdown" class="dropdown-menu position-absolute w-100" style="max-height: 200px; overflow-y: auto; display: none;"></div>
                            <div id="selectedAnnouncementMembers" class="mt-2" style="display:flex; flex-wrap:wrap; gap:6px;"></div>
                        </div>
                    </div>
                    <!-- Announcement Name -->
                    <div class="mb-3">
                        <label for="announcement_name" class="form-label">Announcement Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="announcement_name" name="announcement_name" placeholder="Enter announcement name" required>
                    </div>
                    <!-- Announcement Description with Emoji -->
                    <div class="mb-3">
                        <label for="announcement_description" class="form-label">Announcement Description <span class="text-danger">*</span></label>
                        <div class="input-group mb-2">
                            <textarea class="form-control" id="announcement_description" name="announcement_description" rows="3" placeholder="Write announcement..." required></textarea>
                        </div>
                        <!-- Emoji Box: always visible under textarea -->
                        <div id="emojiBox" class="border rounded p-2 mb-2" style="max-width:100%; background:#fff; position:relative; z-index:1; display:flex; flex-wrap:wrap; gap:6px;">
                            <span class="emoji">😀</span><span class="emoji">😁</span><span class="emoji">😂</span><span class="emoji">🤣</span><span class="emoji">😃</span><span class="emoji">😄</span><span class="emoji">😅</span><span class="emoji">😆</span><span class="emoji">😉</span><span class="emoji">😊</span><span class="emoji">😋</span><span class="emoji">😎</span><span class="emoji">😍</span><span class="emoji">😘</span><span class="emoji">🥰</span><span class="emoji">😗</span><span class="emoji">😙</span><span class="emoji">😚</span><span class="emoji">🙂</span><span class="emoji">🤗</span><span class="emoji">🤩</span><span class="emoji">🤔</span><span class="emoji">🤨</span><span class="emoji">😐</span><span class="emoji">😑</span><span class="emoji">😶</span><span class="emoji">🙄</span><span class="emoji">😏</span><span class="emoji">😣</span><span class="emoji">😥</span><span class="emoji">😮</span><span class="emoji">🤐</span><span class="emoji">😯</span><span class="emoji">😪</span><span class="emoji">😫</span><span class="emoji">🥱</span><span class="emoji">😴</span><span class="emoji">😌</span><span class="emoji">😛</span><span class="emoji">😜</span><span class="emoji">😝</span><span class="emoji">🤤</span><span class="emoji">😒</span><span class="emoji">😓</span><span class="emoji">😔</span><span class="emoji">😕</span><span class="emoji">🙃</span><span class="emoji">🤑</span><span class="emoji">😲</span><span class="emoji">☹️</span><span class="emoji">🙁</span><span class="emoji">😖</span><span class="emoji">😞</span><span class="emoji">😟</span><span class="emoji">😤</span><span class="emoji">😢</span><span class="emoji">😭</span><span class="emoji">😦</span><span class="emoji">�</span><span class="emoji">😨</span><span class="emoji">😩</span><span class="emoji">🤯</span><span class="emoji">😬</span><span class="emoji">😰</span><span class="emoji">😱</span><span class="emoji">🥵</span><span class="emoji">🥶</span><span class="emoji">😳</span><span class="emoji">🤪</span><span class="emoji">😵</span><span class="emoji">😡</span><span class="emoji">😠</span><span class="emoji">🤬</span><span class="emoji">😷</span><span class="emoji">🤒</span><span class="emoji">🤕</span><span class="emoji">🤢</span><span class="emoji">🤮</span><span class="emoji">🤧</span><span class="emoji">🥳</span><span class="emoji">🥺</span><span class="emoji">🤠</span><span class="emoji">🥸</span><span class="emoji">😇</span><span class="emoji">🤡</span><span class="emoji">💩</span><span class="emoji">👻</span><span class="emoji">�</span><span class="emoji">☠️</span><span class="emoji">👽</span><span class="emoji">👾</span><span class="emoji">🤖</span><span class="emoji">🎃</span><span class="emoji">😺</span><span class="emoji">😸</span><span class="emoji">😹</span><span class="emoji">😻</span><span class="emoji">😼</span><span class="emoji">😽</span><span class="emoji">🙀</span><span class="emoji">😿</span><span class="emoji">😾</span><span class="emoji">👍</span><span class="emoji">👎</span><span class="emoji">👏</span><span class="emoji">🙌</span><span class="emoji">👐</span><span class="emoji">🤲</span><span class="emoji">🙏</span><span class="emoji">💪</span><span class="emoji">🦾</span><span class="emoji">🦵</span><span class="emoji">🦶</span><span class="emoji">👋</span><span class="emoji">🤙</span><span class="emoji">💡</span><span class="emoji">🔥</span><span class="emoji">�</span><span class="emoji">🎉</span><span class="emoji">✅</span>
                        </div>
                    </div>
                    <!-- Date -->
                    <div class="mb-3">
                        <label for="announcement_date" class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="announcement_date" name="announcement_date" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <!-- Error/Success Messages -->
                    <div id="announcementMessage" class="alert d-none"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="submitAnnouncement">Submit</button>
            </div>
        </div>
    </div>
</div>
        </div>
    </div>
</header>
<!-- Call & Meet Modal -->
<div class="modal fade" id="callMeetModal" tabindex="-1" aria-labelledby="callMeetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 520px;">
        <div class="modal-content call-meet-modal-content">
            <div class="modal-header border-0" style="background: linear-gradient(90deg, #ff9800 0%, #ffc107 100%); border-radius: 1.5rem 1.5rem 0 0;">
                <h5 class="modal-title fw-bold text-white" id="callMeetModalLabel">
                    <i class="ti ti-phone" style="font-size: 32px; vertical-align: middle; margin-right: 10px;"></i>
                    Call & Meet
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-flex flex-row justify-content-center align-items-center gap-4 p-4" style="background: #fff; border-radius: 0 0 1.5rem 1.5rem;">
                <button type="button" class="call-meet-btn call-btn d-flex flex-column align-items-center justify-content-center" id="callOptionBtn">
                    <span class="call-meet-icon">
                        <i class="ti ti-phone-call"></i>
                    </span>
                    <span class="call-meet-label">Call</span>
                </button>
                <button type="button" class="call-meet-btn meet-btn d-flex flex-column align-items-center justify-content-center" id="meetOptionBtn">
                    <span class="call-meet-icon">
                        <i class="ti ti-users"></i>
                    </span>
                    <span class="call-meet-label">Meet</span>
                </button>
            </div>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    var meetBtn = document.getElementById('meetOptionBtn');
    if (meetBtn) {
        meetBtn.addEventListener('click', function() {
            window.open('https://meet.google.com/new', '_blank');
        });
    }
    var callBtn = document.getElementById('callOptionBtn');
    if (callBtn) {
        callBtn.addEventListener('click', function() {
            // Universal WhatsApp link: opens app on mobile, web on desktop
            window.open('https://wa.me/', '_blank');
        });
    }
});
</script>

<style>
.call-meet-modal-content {
    border-radius: 1.5rem;
    box-shadow: 0 8px 32px rgba(0,0,0,0.18);
    border: none;
    overflow: hidden;
    animation: popIn 0.3s cubic-bezier(.68,-0.55,.27,1.55);
}
.call-meet-btn {
    width: 180px;
    height: 160px;
    border-radius: 1rem;
    background: linear-gradient(90deg, #fff 0%, #f7f7f7 100%);
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    border: none;
    transition: box-shadow 0.2s, transform 0.2s;
    font-weight: 600;
    font-size: 1.25rem;
    cursor: pointer;
    position: relative;
}
.call-meet-btn .call-meet-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
    font-size: 54px;
    color: #fff;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ff9800 0%, #ffc107 100%);
    box-shadow: 0 2px 8px rgba(255,152,0,0.12);
}
.meet-btn .call-meet-icon {
    background: linear-gradient(135deg, #2196f3 0%, #00b8ff 100%);
    box-shadow: 0 2px 8px rgba(33,150,243,0.12);
}
.call-meet-btn .call-meet-label {
    color: #333;
    font-size: 1.25rem;
    font-weight: 700;
    letter-spacing: 1px;
}
.call-meet-btn:hover {
    box-shadow: 0 6px 24px rgba(0,0,0,0.16);
    transform: translateY(-4px) scale(1.04);
}
.call-btn .call-meet-icon {
    background: linear-gradient(135deg, #ff9800 0%, #ffc107 100%);
}
.meet-btn .call-meet-icon {
    background: linear-gradient(135deg, #2196f3 0%, #00b8ff 100%);
}
@keyframes popIn {
    0% { transform: scale(0.8); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}
</style>
<!-- Header Apps Styles -->
<style>
    .apps-toolbar { 
        display: flex; 
        align-items: center; 
        gap: 8px; 
        flex-wrap: wrap; 
        padding: 8px 12px;
        background: linear-gradient(141.55deg, #ff6f28 3.46%, #ff6f28 99.86%), #ff6f28;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(255, 111, 40, 0.3);
    }
    .header-app-item { 
        display: inline-flex; 
        align-items: center; 
        justify-content: center; 
        width: 36px; 
        height: 36px; 
        border-radius: 8px; 
        transition: all 0.2s ease; 
        cursor: pointer; 
        position: relative;
        background: rgba(255, 255, 255, 0.4);
        backdrop-filter: blur(10px);
    }
    .header-app-item:hover { 
        background-color: rgba(255, 255, 255, 0.6); 
        transform: scale(1.1);
        box-shadow: 0 2px 8px rgba(255, 255, 255, 0.3);
    }
    .header-app-icon { 
        width: 24px; 
        height: 24px; 
        object-fit: contain;
    }
    /* Tooltip styling */
    .header-app-item[title] {
        position: relative;
    }
    .header-app-item[title]:hover::after {
        content: attr(title);
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        margin-top: 8px;
        padding: 6px 10px;
        background-color: rgba(0, 0, 0, 0.85);
        color: #fff;
        font-size: 12px;
        white-space: nowrap;
        border-radius: 4px;
        pointer-events: none;
        z-index: 10000;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }
    .header-app-item[title]:hover::before {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        margin-top: 2px;
        border: 5px solid transparent;
        border-bottom-color: rgba(0, 0, 0, 0.85);
        pointer-events: none;
        z-index: 10001;
    }
    .submenu-item { font-size: 14px; color: #202124; cursor: pointer; transition: background-color 0.2s ease; }
    .submenu-item:hover { background-color: #f1f3f4; }
    @media (max-width: 768px) {
        .apps-toolbar { gap: 6px; padding: 6px 8px; }
        .header-app-item { width: 32px; height: 32px; }
        .header-app-icon { width: 20px; height: 20px; }
    }
</style>

<style>
.call-meet-modal-content {
    border-radius: 1rem;
    box-shadow: 0 8px 32px rgba(0,0,0,0.18);
    border: none;
    overflow: hidden;
    animation: popIn 0.3s cubic-bezier(.68,-0.55,.27,1.55);
}
.btn-gradient-orange {
    background: linear-gradient(90deg, #ff9800 0%, #ffc107 100%);
    color: #fff;
    border: none;
    font-weight: 600;
    transition: box-shadow 0.2s, transform 0.2s;
}
.btn-gradient-orange:hover {
    box-shadow: 0 4px 16px rgba(255,152,0,0.18);
    transform: translateY(-2px) scale(1.03);
}
.btn-gradient-blue {
    background: linear-gradient(90deg, #2196f3 0%, #00b8ff 100%);
    color: #fff;
    border: none;
    font-weight: 600;
    transition: box-shadow 0.2s, transform 0.2s;
}
.btn-gradient-blue:hover {
    box-shadow: 0 4px 16px rgba(33,150,243,0.18);
    transform: translateY(-2px) scale(1.03);
}
@keyframes popIn {
    0% { transform: scale(0.8); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}
</style>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewModalLabel">💡👥📝🌈 Submit Team Member Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="reviewForm">
                    @csrf
                                        <!-- Review Given By (Readonly) -->
                    <div class="mb-3">
                        <label for="reviewer_name" class="form-label">Review Given By</label>
                        <input type="text" 
                               class="form-control" 
                               id="reviewer_name" 
                               name="reviewer_name" 
                               value="{{ Auth::user()->name }}"
                               readonly>
                    </div>
                    <!-- Employee Dropdown -->
                    <div class="mb-3">
                        <label for="reviewee_name" class="form-label">Select Team Member <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <input type="text" 
                                   class="form-control" 
                                   id="reviewee_name" 
                                   name="reviewee_name" 
                                   placeholder="Type employee name..."
                                   autocomplete="off"
                                   required>
                            <input type="hidden" id="reviewee_id" name="reviewee_id" required>
                            <div id="employeeDropdown" class="dropdown-menu position-absolute w-100" style="max-height: 200px; overflow-y: auto; display: none;">
                            </div>
                        </div>
                    </div>

                    <!-- Review Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Improvement Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="4" 
                                  placeholder="Write your review here..." required></textarea>
                    </div>

                    <!-- Star Rating -->
                    <div class="mb-3">
                        <label class="form-label">Rating <span class="text-danger">*</span></label>
                        <div class="star-rating" id="starRating">
                            <input type="hidden" id="rating" name="rating" value="" required>
                            <div class="stars">
                                <i class="ti ti-star star" data-rating="1"></i>
                                <i class="ti ti-star star" data-rating="2"></i>
                                <i class="ti ti-star star" data-rating="3"></i>
                                <i class="ti ti-star star" data-rating="4"></i>
                                <i class="ti ti-star star" data-rating="5"></i>
                            </div>
                            <small class="text-muted">Click on stars to rate (1-5)</small>
                        </div>
                    </div>

                    <!-- Screenshot Upload -->
                    <div class="mb-3">
                        <label for="screenshot" class="form-label">Screenshot (Optional)</label>
                        <input type="file" class="form-control" id="screenshot" name="screenshot" accept="image/*">
                        <small class="text-muted">Upload a screenshot to support your review (PNG, JPG, JPEG only)</small>
                        <div id="screenshotPreview" style="display: none; margin-top: 10px;">
                            <img id="previewImage" src="" alt="Screenshot Preview" style="max-width: 200px; max-height: 200px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                    </div>

                    <!-- Error/Success Messages -->
                    <div id="reviewMessage" class="alert d-none"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="submitReview">Submit Review</button>
            </div>
        </div>
    </div>
</div>

<!-- DAR Modal -->
<div class="modal fade" id="darModal" tabindex="-1" aria-labelledby="darModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="darModalLabel">Time Management Report (TMR)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="darForm">
                    @csrf
                    <!-- Date Section -->
                    <div class="mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Report Date</label>
                                <input type="date" class="form-control" name="report_date" id="reportDate" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Task Entry Section -->
                    <div class="mb-4">
                        <h6 class="mb-3">Task Details</h6>
                        <div id="taskContainer">
                            <div class="task-row row mb-3">
                                <div class="col-md-2">
                                    <label class="form-label">Group Name</label>
                                    <input type="text" class="form-control task-group" name="tasks[0][group_name]" placeholder="Enter group name" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Task Description</label>
                                    <input type="text" class="form-control task-description" name="tasks[0][description]" placeholder="Enter task description" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Time Spent (Min)</label>
                                    <input type="number" class="form-control task-time" name="tasks[0][time_spent]" placeholder="20" min="1" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Task Status</label>
                                    <select class="form-control task-status" name="tasks[0][status]" required>
                                        <option value="">Select Status</option>
                                        <option value="Complete">Complete</option>
                                        <option value="Pending">Pending</option>
                                        <option value="In Progress">In Progress</option>
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger btn-sm remove-task" style="display: none;">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" class="btn btn-outline-primary btn-sm" id="addTask">
                            <i class="ti ti-plus"></i> Add Task
                        </button>
                    </div>

                    <!-- Total Time Display -->
                    <div class="mb-4">
                        <div class="alert alert-info">
                            <strong>Total Time: <span id="totalTime">0h 0m</span></strong>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-end">
                        <button type="button" class="btn btn-primary" id="submitDAR">
                            Submit
                        </button>
                    </div>

                    <!-- Error/Success Messages -->
                    <div id="darMessage" class="alert d-none mt-3"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ADDED: Incentive Modal -->
<div class="modal fade" id="incentiveModal" tabindex="-1" aria-labelledby="incentiveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="incentiveModalLabel">Give Incentive to Team Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="incentiveForm">
                    @csrf
                    <!-- Incentive Given By (Readonly) -->
                    <div class="mb-3">
                        <label for="incentive_giver_name" class="form-label">Incentive Given By</label>
                        <input type="text" 
                               class="form-control" 
                               id="incentive_giver_name" 
                               name="incentive_giver_name" 
                               value="{{ Auth::user()->name }}"
                               readonly>
                    </div>
                    
                    <!-- Team Member Dropdown -->
                    <div class="mb-3">
                        <label for="incentive_team_member" class="form-label">Select Team Member <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <input type="text" 
                                   class="form-control" 
                                   id="incentive_team_member" 
                                   name="incentive_team_member" 
                                   placeholder="Type employee name..."
                                   autocomplete="off"
                                   required>
                            <input type="hidden" id="incentive_team_member_id" name="incentive_team_member_id" required>
                            <div id="incentiveEmployeeDropdown" class="dropdown-menu position-absolute w-100" style="max-height: 200px; overflow-y: auto; display: none;">
                            </div>
                        </div>
                    </div>

                    <!-- Incentive Amount -->
                    <div class="mb-3">
                        <label for="incentive_amount" class="form-label">Incentive Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">â‚¹</span>
                            <input type="number" 
                                   class="form-control" 
                                   id="incentive_amount" 
                                   name="incentive_amount" 
                                   placeholder="0.00"
                                   step="0.01"
                                   min="0"
                                   required>
                        </div>
                    </div>

                    <!-- Date Range -->
                    <div class="mb-3">
                        <label class="form-label">Date Range <span class="text-danger">*</span></label>
                        <div class="row">
                            <!--<div class="col-md-6">-->
                            <!--    <label for="start_date" class="form-label">Start Date</label>-->
                            <!--    <input type="date" -->
                            <!--           class="form-control" -->
                            <!--           id="start_date" -->
                            <!--           name="start_date" -->
                            <!--           required>-->
                            <!--</div>-->
                            <div class="col-md-12">
                                <label for="end_date" class="form-label">Deadline</label>
                                <input type="date" 
                                       class="form-control" 
                                       id="end_date" 
                                       name="end_date" 
                                       required>
                            </div>
                        </div>
                    </div>

                    <!-- Incentive Description -->
                    <div class="mb-3">
                        <label for="incentive_description" class="form-label">Conditions<span class="text-danger">*</span></label>
                        <textarea class="form-control" 
                                  id="incentive_description" 
                                  name="incentive_description" 
                                  rows="4" 
                                  placeholder="Describe the reason for this incentive..." 
                                  required></textarea>
                    </div>

                    <!-- Error/Success Messages -->
                    <div id="incentiveMessage" class="alert d-none"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-info" id="submitIncentive">Give Incentive</button>
            </div>
        </div>
    </div>
</div>

<!-- ADDED: Deduction Modal -->
<div class="modal fade" id="deductionModal" tabindex="-1" aria-labelledby="deductionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deductionModalLabel">Apply Deduction to Team Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="deductionForm">
                    @csrf
                    <!-- Deduction Applied By (Readonly) -->
                    <div class="mb-3">
                        <label for="deduction_giver_name" class="form-label">Deduction Applied By</label>
                        <input type="text" 
                               class="form-control" 
                               id="deduction_giver_name" 
                               name="deduction_giver_name" 
                               value="{{ Auth::user()->name }}"
                               readonly>
                    </div>
                    
                    <!-- Team Member Dropdown -->
                    <div class="mb-3">
                        <label for="deduction_team_member" class="form-label">Select Team Member <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <input type="text" 
                                   class="form-control" 
                                   id="deduction_team_member" 
                                   name="deduction_team_member" 
                                   placeholder="Type employee name..."
                                   autocomplete="off"
                                   required>
                            <input type="hidden" id="deduction_team_member_id" name="deduction_team_member_id" required>
                            <div id="deductionEmployeeDropdown" class="dropdown-menu position-absolute w-100" style="max-height: 200px; overflow-y: auto; display: none;">
                            </div>
                        </div>
                    </div>

                    <!-- Deduction Amount -->
                    <div class="mb-3">
                        <label for="deduction_amount" class="form-label">Deduction Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" 
                                   class="form-control" 
                                   id="deduction_amount" 
                                   name="deduction_amount" 
                                   placeholder="0.00"
                                   step="0.01"
                                   min="0"
                                   required>
                        </div>
                    </div>

                    <!-- Date Range -->
                    <div class="mb-3">
                        <label class="form-label">Date Range <span class="text-danger">*</span></label>
                        <div class="row">
                            <div class="col-md-12">
                                <label for="deduction_date" class="form-label">Deduction Date</label>
                                <input type="date" 
                                       class="form-control" 
                                       id="deduction_date" 
                                       name="deduction_date" 
                                       value="{{ date('Y-m-d') }}"
                                       required>
                            </div>
                        </div>
                    </div>

                    <!-- Deduction Description -->
                    <div class="mb-3">
                        <label for="deduction_description" class="form-label">Reason for Deduction <span class="text-danger">*</span></label>
                        <textarea class="form-control" 
                                  id="deduction_description" 
                                  name="deduction_description" 
                                  rows="4" 
                                  placeholder="Describe the reason for this deduction..." 
                                  required></textarea>
                    </div>

                    <!-- Error/Success Messages -->
                    <div id="deductionMessage" class="alert d-none"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="submitDeduction">Apply Deduction</button>
            </div>
        </div>
    </div>
</div>

<!-- Screenshot Modal -->
<div class="modal fade" id="screenshotModal" tabindex="-1" aria-labelledby="screenshotModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="screenshotModalLabel">📸 Screenshot Tool</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <h6 class="mb-3">Choose Screenshot Option:</h6>
                        
                        <!-- Screenshot Options -->
                        <div class="d-grid gap-3">
                            <!-- Take New Screenshot -->
                            <div class="card border-primary">
                                <div class="card-body text-center">
                                    <i class="ti ti-camera" style="font-size: 48px; color: #0d6efd;"></i>
                                    <h6 class="card-title mt-2">Take Screenshot</h6>
                                    <p class="card-text text-muted">Capture your screen instantly</p>
                                    <button type="button" class="btn btn-primary" id="takeScreenshot">
                                        <i class="ti ti-camera"></i> Take Screenshot
                                    </button>
                                </div>
                            </div>

                            <!-- Upload Screenshot -->
                            <!-- <div class="card border-success">
                                <div class="card-body text-center">
                                    <i class="ti ti-upload" style="font-size: 48px; color: #198754;"></i>
                                    <h6 class="card-title mt-2">Upload Screenshot</h6>
                                    <p class="card-text text-muted">Select an image file from your device</p>
                                    <input type="file" id="screenshotUpload" accept="image/*" style="display: none;">
                                    <button type="button" class="btn btn-success" id="uploadScreenshotBtn">
                                        <i class="ti ti-upload"></i> Upload Image
                                    </button>
                                </div>
                            </div> -->

                            <!-- Screen Recording -->
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <i class="ti ti-video" style="font-size: 48px; color: #ffc107;"></i>
                                    <h6 class="card-title mt-2">Screen Recording</h6>
                                    <p class="card-text text-muted">Record your screen activity</p>
                                    <button type="button" class="btn btn-warning" id="startRecording">
                                        <i class="ti ti-video"></i> Start Recording
                                    </button>
                                    <button type="button" class="btn btn-danger d-none" id="stopRecording">
                                        <i class="ti ti-square"></i> Stop Recording
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Screenshot Preview Area -->
                        <div id="screenshotPreviewArea" class="mt-4" style="display: none;">
                            <h6>Preview:</h6>
                            <div class="text-center">
                                <img id="screenshotPreviewImage" src="" alt="Screenshot Preview" class="img-fluid border rounded" style="max-height: 300px;">
                                <div class="mt-3">
                                    <button type="button" class="btn btn-primary" id="saveScreenshot">
                                        <i class="ti ti-device-floppy"></i> Save Screenshot
                                    </button>
                                    <button type="button" class="btn btn-secondary" id="retakeScreenshot">
                                        <i class="ti ti-refresh"></i> Retake
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Recording Preview Area -->
                        <div id="recordingPreviewArea" class="mt-4" style="display: none;">
                            <h6>Recording Preview:</h6>
                            <div class="text-center">
                                <video id="recordingPreview" controls class="img-fluid border rounded" style="max-height: 300px;"></video>
                                <div class="mt-3">
                                    <button type="button" class="btn btn-primary" id="saveRecording">
                                        <i class="ti ti-device-floppy"></i> Save Recording
                                    </button>
                                    <button type="button" class="btn btn-secondary" id="newRecording">
                                        <i class="ti ti-refresh"></i> New Recording
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Status Messages -->
                        <div id="screenshotMessage" class="alert d-none mt-3"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Announcement Modal Styles */
#announcementModal .modal-content {
    border-radius: 0.75rem;
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
}
#emojiBox {
    cursor: pointer;
    font-size: 22px;
    background: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    max-height: 140px;
    overflow-y: auto;
}
#emojiBox .emoji {
    margin: 4px;
    padding: 2px 6px;
    border-radius: 4px;
    transition: background 0.2s;
}
#emojiBox .emoji:hover {
    background: #f0f0f0;
}
.star-rating .stars {
    display: flex;
    gap: 5px;
    margin-bottom: 5px;
}
.star-rating .star {
    font-size: 24px;
    color: #ddd;
    cursor: pointer;
    transition: color 0.2s;
}

.star-rating .star:hover,
.star-rating .star.active {
    color: #ffc107;
}

.star-rating .star.hover {
    color: #ffc107;
}

/* Searchable dropdown styles */
#employeeDropdown {
    z-index: 1050;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

#employeeDropdown .dropdown-item {
    padding: 0.5rem 1rem;
    cursor: pointer;
    border: none;
}

#employeeDropdown .dropdown-item:hover {
    background-color: #f8f9fa;
}

#employeeDropdown .dropdown-item-text {
    padding: 0.5rem 1rem;
    color: #6c757d;
}

/* DAR Modal Styles */
.task-row {
    border: 1px solid #e9ecef;
    border-radius: 0.375rem;
    padding: 15px;
    margin-bottom: 15px;
    background-color: #f8f9fa;
}

.task-row:last-child {
    margin-bottom: 0;
}

#addTask {
    margin-top: 10px;
}

.remove-task {
    height: 38px;
    width: 38px;
}

#totalTime {
    font-size: 1.1em;
    color: #0d6efd;
}

/* Deduction Modal Dropdown Styles */
#deductionEmployeeDropdown {
    z-index: 1050;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

#deductionEmployeeDropdown .dropdown-item {
    padding: 0.5rem 1rem;
    cursor: pointer;
    border: none;
}

#deductionEmployeeDropdown .dropdown-item:hover {
    background-color: #f8f9fa;
}

#deductionEmployeeDropdown .dropdown-item-text {
    padding: 0.5rem 1rem;
    color: #6c757d;
}

/* Screenshot Modal Styles */
#screenshotModal .modal-content {
    border-radius: 0.75rem;
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
}

#screenshotModal .card {
    transition: transform 0.2s, box-shadow 0.2s;
}

#screenshotModal .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

#screenshotPreviewArea img, #recordingPreviewArea video {
    border: 2px solid #dee2e6;
    border-radius: 8px;
}

.recording-indicator {
    display: inline-block;
    width: 12px;
    height: 12px;
    background-color: #dc3545;
    border-radius: 50%;
    animation: blink 1s infinite;
    margin-right: 8px;
}

@keyframes blink {
    0%, 50% { opacity: 1; }
    51%, 100% { opacity: 0; }
}
</style>
<style>
.modal-backdrop.show {
    opacity: 0.3 !important;
}
.modal-backdrop {
    display: none !important;
}
</style>
<script>
$(document).ready(function() {
       // Announcement Modal: Load employees for team member dropdown
    let announcementEmployees = [];
    $('#announcementModal').on('show.bs.modal', function() {
        $.ajax({
            url: '{{ route("reviews.employees") }}',
            method: 'GET',
            success: function(response) {
                announcementEmployees = response.employees || response;
            },
            error: function(xhr) {
                announcementEmployees = [];
            }
        });
    });

    // Announcement Modal: Searchable dropdown for team member (multi-select)
    let selectedAnnouncementMembers = [];
    let allAnnouncementMembersSelected = false;
    $('#announcement_team_member').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        const dropdown = $('#announcementEmployeeDropdown');
        if (searchTerm.length === 0) {
            dropdown.hide();
            return;
        }
        const filteredEmployees = announcementEmployees.filter(employee => 
            employee.name.toLowerCase().includes(searchTerm)
            && !selectedAnnouncementMembers.some(m => m.id === employee.id)
        );
        if (filteredEmployees.length > 0) {
            let dropdownHtml = '';
            filteredEmployees.forEach(employee => {
                dropdownHtml += `<a class="dropdown-item announcement-employee-item" href="#" data-id="${employee.id}" data-name="${employee.name}">${employee.name}</a>`;
            });
            dropdown.html(dropdownHtml).show();
        } else {
            dropdown.html('<span class="dropdown-item-text text-muted">No employees found</span>').show();
        }
    });

    // Select All Team Members button
    $(document).on('click', '#selectAllAnnouncementMembers', function(e) {
        e.preventDefault();
        if (announcementEmployees.length === 0) {
            showAnnouncementMessage('No team members found to select.', 'danger');
            return;
        }
        selectedAnnouncementMembers = announcementEmployees.map(emp => ({id: emp.id, name: emp.name}));
        allAnnouncementMembersSelected = true;
        updateSelectedAnnouncementMembers();
    });

    // Announcement Modal: Handle employee selection (multi)
    $(document).on('click', '.announcement-employee-item', function(e) {
        e.preventDefault();
        const employeeId = $(this).data('id');
        const employeeName = $(this).data('name');
        // Add to selected list if not already present
        if (!selectedAnnouncementMembers.some(m => m.id === employeeId)) {
            selectedAnnouncementMembers.push({id: employeeId, name: employeeName});
            updateSelectedAnnouncementMembers();
        }
        $('#announcement_team_member').val('');
        $('#announcementEmployeeDropdown').hide();
    });

    // Remove selected member
    $(document).on('click', '.remove-announcement-member', function(e) {
        e.preventDefault();
        const employeeId = $(this).data('id');
        selectedAnnouncementMembers = selectedAnnouncementMembers.filter(m => m.id !== employeeId);
        allAnnouncementMembersSelected = false;
        updateSelectedAnnouncementMembers();
    });

    // === Announcement Multi-Select + All Team Members Feature (Marked Block) START ===
    function updateSelectedAnnouncementMembers() {
        const container = $('#selectedAnnouncementMembers');
        if (selectedAnnouncementMembers.length === 0) {
            container.html('');
            $('#announcement_team_member_ids').val('');
            return;
        }
        let html = '';
        if (allAnnouncementMembersSelected) {
            html = `<span class='badge bg-success ms-2'>All Team Members Selected</span>`;
        } else {
            selectedAnnouncementMembers.forEach(member => {
                html += `<span class="badge bg-primary me-1 mb-1">${member.name} <a href="#" class="text-white ms-1 remove-announcement-member" data-id="${member.id}" style="text-decoration:none;">&times;</a></span>`;
            });
        }
        container.html(html);
        $('#announcement_team_member_ids').val(selectedAnnouncementMembers.map(m => m.id).join(','));
    }
    // === Announcement Multi-Select + All Team Members Feature (Marked Block) END ===

    function updateSelectedAnnouncementMembers() {
        const container = $('#selectedAnnouncementMembers');
        if (selectedAnnouncementMembers.length === 0) {
            container.html('');
            $('#announcement_team_member_ids').val('');
            return;
        }
        let html = '';
        selectedAnnouncementMembers.forEach(member => {
            html += `<span class="badge bg-primary me-1 mb-1">${member.name} <a href="#" class="text-white ms-1 remove-announcement-member" data-id="${member.id}" style="text-decoration:none;">&times;</a></span>`;
        });
        if (allAnnouncementMembersSelected) {
            html += `<span class='badge bg-success ms-2'>All Team Members Selected</span>`;
        }
        container.html(html);
        $('#announcement_team_member_ids').val(selectedAnnouncementMembers.map(m => m.id).join(','));
    }

    // Hide dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#announcement_team_member, #announcementEmployeeDropdown').length) {
            $('#announcementEmployeeDropdown').hide();
        }
    });

    // Clear input only (do not clear selected members)
    $('#announcement_team_member').on('keyup', function() {
        // No action needed for multi-select
    });
    // Announcement Modal: Emoji Picker
    // Emoji click: insert emoji into textarea
    $(document).on('click', '.emoji', function() {
        const emoji = $(this).text();
        const textarea = $('#announcement_description');
        textarea.val(textarea.val() + emoji);
    });

    // Announcement Modal: Submit
        $('#submitAnnouncement').on('click', function() {
            const formData = {
                _token: $('input[name="_token"]').val(),
                announcement_group: $('#announcement_group').val(),
                announcement_name: $('#announcement_name').val(),
                announcement_description: $('#announcement_description').val(),
                announcement_date: $('#announcement_date').val(),
                announcement_team_member_ids: $('#announcement_team_member_ids').val()
            };
            // Basic validation
            if (!formData.announcement_group) {
                showAnnouncementMessage('Please select a group', 'danger');
                return;
            }
            if (!formData.announcement_name.trim()) {
                showAnnouncementMessage('Please enter announcement name', 'danger');
                return;
            }
            if (!formData.announcement_description.trim()) {
                showAnnouncementMessage('Please enter announcement description', 'danger');
                return;
            }
            if (!formData.announcement_date) {
                showAnnouncementMessage('Please select a date', 'danger');
                return;
            }
            if (!formData.announcement_team_member_ids || formData.announcement_team_member_ids.trim() === '') {
                showAnnouncementMessage('Please select at least one team member', 'danger');
                return;
            }
            // Submit the announcement (AJAX)
            $.ajax({
                url: '/announcement/store',
                method: 'POST',
                data: formData,
                beforeSend: function() {
                    $('#submitAnnouncement').prop('disabled', true).text('Submitting...');
                },
                success: function(response) {
                    showAnnouncementMessage('Announcement created successfully!', 'success');
                    playAnnouncementSound();
                    setTimeout(function() {
                        $('#announcementModal').modal('hide');
                        resetAnnouncementForm();
                    }, 1500);
                },
                error: function(xhr) {
                    let message = 'Error creating announcement';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        message = errors.join(', ');
                    }
                    showAnnouncementMessage(message, 'danger');
                },
                complete: function() {
                    $('#submitAnnouncement').prop('disabled', false).text('Submit');
                }
            });
        });
    // Announcement Modal: Reset form when modal is hidden
    $('#announcementModal').on('hidden.bs.modal', function() {
        resetAnnouncementForm();
    });
    function resetAnnouncementForm() {
        $('#announcementForm')[0].reset();
        selectedAnnouncementMembers = [];
        updateSelectedAnnouncementMembers();
        hideAnnouncementMessage();
    }
    function showAnnouncementMessage(message, type) {
        const messageDiv = $('#announcementMessage');
        messageDiv.removeClass('d-none alert-success alert-danger alert-info')
                  .addClass(`alert-${type}`)
                  .text(message);
    }
    function hideAnnouncementMessage() {
        $('#announcementMessage').addClass('d-none');
    }
    // Play notification sound (simulate)
    function playAnnouncementSound() {
        const audio = new Audio('https://cdn.pixabay.com/audio/2022/07/26/audio_124b7b2b48.mp3');
        audio.play();
    }
    // Announcement History (simulate)
    $('#announcementHistoryBtn').on('click', function(e) {
        e.preventDefault();
        window.location.href = '/announcement/history';
    });
    // Load employees when modal opens
    $('#reviewModal').on('show.bs.modal', function() {
        loadEmployees();
    });
    // Star Rating System
    let selectedRating = 0;
    
    $('.star').on('mouseenter', function() {
        const rating = $(this).data('rating');
        highlightStars(rating);
    });
    
    $('.stars').on('mouseleave', function() {
        highlightStars(selectedRating);
    });
    
    $('.star').on('click', function() {
        selectedRating = $(this).data('rating');
        $('#rating').val(selectedRating);
        highlightStars(selectedRating);
    });
    
    function highlightStars(rating) {
        $('.star').each(function(index) {
            if (index < rating) {
                $(this).addClass('active');
            } else {
                $(this).removeClass('active');
            }
        });
    }

    // Load employees function
    let employees = [];
    
    function loadEmployees() {
        $.ajax({
            url: '{{ route("reviews.employees") }}',
            method: 'GET',
            success: function(response) {
                // Handle response structure - check if response.employees exists
                employees = response.employees || response;
                
                if (!employees || !Array.isArray(employees)) {
                    console.log('Invalid employee data:', response);
                    showMessage('Error loading employee data', 'danger');
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error:', xhr.responseText);
                showMessage('Error loading employees', 'danger');
            }
        });
    }
    // Searchable dropdown functionality
    $('#reviewee_name').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        const dropdown = $('#employeeDropdown');
        
        if (searchTerm.length === 0) {
            dropdown.hide();
            $('#reviewee_id').val('');
            return;
        }
        
        const filteredEmployees = employees.filter(employee => 
            employee.name.toLowerCase().includes(searchTerm)
        );
        
        if (filteredEmployees.length > 0) {
            let dropdownHtml = '';
            filteredEmployees.forEach(employee => {
                dropdownHtml += `<a class="dropdown-item employee-item" href="#" data-id="${employee.id}" data-name="${employee.name}">${employee.name}</a>`;
            });
            dropdown.html(dropdownHtml).show();
        } else {
            dropdown.html('<span class="dropdown-item-text text-muted">No employees found</span>').show();
        }
    });
    // Handle employee selection
    $(document).on('click', '.employee-item', function(e) {
        e.preventDefault();
        const employeeId = $(this).data('id');
        const employeeName = $(this).data('name');
        
        $('#reviewee_name').val(employeeName);
        $('#reviewee_id').val(employeeId);
        $('#employeeDropdown').hide();
    });
    // Hide dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#reviewee_name, #employeeDropdown').length) {
            $('#employeeDropdown').hide();
        }
    });

    // Clear hidden field when input is manually cleared
    $('#reviewee_name').on('keyup', function() {
        if ($(this).val() === '') {
            $('#reviewee_id').val('');
        }
        
    });

    // Screenshot preview functionality
    $('#screenshot').on('change', function() {
        const file = this.files[0];
        if (file) {
            // Validate file type
            const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
            if (!allowedTypes.includes(file.type)) {
                alert('Please select a valid image file (PNG, JPG, JPEG only)');
                this.value = '';
                $('#screenshotPreview').hide();
                return;
            }
            
            // Validate file size (5MB max)
            if (file.size > 5 * 1024 * 1024) {
                alert('File size should not exceed 5MB');
                this.value = '';
                $('#screenshotPreview').hide();
                return;
            }
            
            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#previewImage').attr('src', e.target.result);
                $('#screenshotPreview').show();
            };
            reader.readAsDataURL(file);
        } else {
            $('#screenshotPreview').hide();
        }
    });

    // Submit review
    $('#submitReview').on('click', function() {
        // Create FormData object for file upload
        const formData = new FormData();
        formData.append('_token', $('input[name="_token"]').val());
        formData.append('reviewee_id', $('#reviewee_id').val());
        formData.append('description', $('#description').val());
        formData.append('rating', $('#rating').val());
        
        // Add screenshot if selected
        const screenshotFile = $('#screenshot')[0].files[0];
        if (screenshotFile) {
            formData.append('screenshot', screenshotFile);
        }

        // Basic validation
        if (!$('#reviewee_id').val()) {
            showMessage('Please select an employee', 'danger');
            return;
        }
        if (!$('#description').val().trim()) {
            showMessage('Please enter a review description', 'danger');
            return;
        }
        if (!$('#rating').val()) {
            showMessage('Please select a rating', 'danger');
            return;
        }

        // Submit the review
        $.ajax({
            url: '{{ route("reviews.store") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#submitReview').prop('disabled', true).text('Submitting...');
            },
            success: function(response) {
                showMessage('Review submitted successfully!', 'success');
                setTimeout(function() {
                    $('#reviewModal').modal('hide');
                    resetForm();
                }, 1500);
            },
            error: function(xhr) {
                let message = 'Error submitting review';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    message = errors.join(', ');
                }
                showMessage(message, 'danger');
            },
            complete: function() {
                $('#submitReview').prop('disabled', false).text('Submit Review');
            }
        });
    });

    // Reset form when modal is hidden
    $('#reviewModal').on('hidden.bs.modal', function() {
        resetForm();
    });
    function resetForm() {
        $('#reviewForm')[0].reset();
        $('#reviewee_id').val('');
        $('#employeeDropdown').hide();
        selectedRating = 0;
        highlightStars(0);
        hideMessage();
        // Clear screenshot preview
        $('#screenshotPreview').hide();
        $('#previewImage').attr('src', '');
    }

    function showMessage(message, type) {
        const messageDiv = $('#reviewMessage');
        messageDiv.removeClass('d-none alert-success alert-danger alert-info')
                  .addClass(`alert-${type}`)
                  .text(message);
    }

    function hideMessage() {
        $('#reviewMessage').addClass('d-none');
    }

    // DAR Modal Functionality
    let taskCounter = 1;

    // Add new task row
    $('#addTask').on('click', function() {
        const newTaskRow = `
            <div class="task-row row mb-3">
                <div class="col-md-2">
                    <label class="form-label">Group Name</label>
                    <input type="text" class="form-control task-group" name="tasks[${taskCounter}][group_name]" placeholder="Enter group name" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Task Description</label>
                    <input type="text" class="form-control task-description" name="tasks[${taskCounter}][description]" placeholder="Enter task description" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Time Spent (Min)</label>
                    <input type="number" class="form-control task-time" name="tasks[${taskCounter}][time_spent]" placeholder="20" min="1" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Task Status</label>
                    <select class="form-control task-status" name="tasks[${taskCounter}][status]" required>
                        <option value="">Select Status</option>
                        <option value="Complete">Complete</option>
                        <option value="Pending">Pending</option>
                        <option value="In Progress">In Progress</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm remove-task">
                        <i class="ti ti-trash"></i>
                    </button>
                </div>
            </div>
        `;
        
        $('#taskContainer').append(newTaskRow);
        taskCounter++;
        updateRemoveButtons();
        calculateTotalTime();
    });

    // Remove task row
    $(document).on('click', '.remove-task', function() {
        $(this).closest('.task-row').remove();
        updateRemoveButtons();
        calculateTotalTime();
    });

    // Update remove buttons visibility
    function updateRemoveButtons() {
        const taskRows = $('.task-row');
        if (taskRows.length > 1) {
            $('.remove-task').show();
        } else {
            $('.remove-task').hide();
        }
    }

    // Calculate total time
    $(document).on('input', '.task-time', function() {
        calculateTotalTime();
    });

    function calculateTotalTime() {
        let totalMinutes = 0;
        $('.task-time').each(function() {
            const minutes = parseInt($(this).val()) || 0;
            totalMinutes += minutes;
        });
        
        const hours = Math.floor(totalMinutes / 60);
        const remainingMinutes = totalMinutes % 60;
        $('#totalTime').text(`${hours}h ${remainingMinutes}m`);
    }

    // Submit DAR
    $('#submitDAR').on('click', function() {
        const reportDate = $('#reportDate').val();
        const tasks = [];
        let isValid = true;

        // Validate report date
        if (!reportDate) {
            showDarMessage('Please select a report date', 'danger');
            return;
        }

        $('.task-row').each(function() {
            const groupName = $(this).find('.task-group').val() ? $(this).find('.task-group').val().trim() : '';
            const description = $(this).find('.task-description').val().trim();
            const timeSpent = $(this).find('.task-time').val();
            const status = $(this).find('.task-status').val();

            if (!groupName || !description || !timeSpent || !status) {
                isValid = false;
                return false;
            }

            tasks.push({
                group_name: groupName,
                description: description,
                time_spent: parseInt(timeSpent),
                status: status
            });
        });

        if (!isValid) {
            showDarMessage('Please fill in all task details', 'danger');
            return;
        }

        if (tasks.length === 0) {
            showDarMessage('Please add at least one task', 'danger');
            return;
        }

        const formData = {
            _token: $('input[name="_token"]').val(),
            report_date: reportDate,
            tasks: tasks
        };

        // Submit the DAR
        $.ajax({
            url: '{{ route("dar.store") }}',
            method: 'POST',
            data: formData,
            beforeSend: function() {
                $('#submitDAR').prop('disabled', true).text('Submitting...');
            },
            success: function(response) {
                showDarMessage('Daily Activity Report submitted successfully!', 'success');
                setTimeout(function() {
                    $('#darModal').modal('hide');
                    resetDarForm();
                }, 1500);
            },
            error: function(xhr) {
                let message = 'Error submitting DAR';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                showDarMessage(message, 'danger');
            },
            complete: function() {
                $('#submitDAR').prop('disabled', false).text('Submit');
            }
        });
    });

    // Reset DAR form when modal is hidden
    $('#darModal').on('hidden.bs.modal', function() {
        resetDarForm();
    });

    function resetDarForm() {
        // Keep only first task row
        $('.task-row').not(':first').remove();
        // Clear first task row
        $('.task-row:first .task-description').val('');
        $('.task-row:first .task-time').val('');
        $('.task-row:first .task-status').val('');
        
        taskCounter = 1;
        updateRemoveButtons();
        calculateTotalTime();
        hideDarMessage();
    }

    function showDarMessage(message, type) {
        const messageDiv = $('#darMessage');
        messageDiv.removeClass('d-none alert-success alert-danger alert-info')
                  .addClass(`alert-${type}`)
                  .text(message);
    }

    function hideDarMessage() {
        $('#darMessage').addClass('d-none');
    }

    // Initialize
    updateRemoveButtons();
    calculateTotalTime();

    // ADDED: Incentive Modal Functionality
    // Load employees when incentive modal opens
    $('#incentiveModal').on('show.bs.modal', function() {
        loadIncentiveEmployees();
    });

    // Load employees function for incentive
    let incentiveEmployees = [];
    
    function loadIncentiveEmployees() {
        $.ajax({
            url: '{{ route("reviews.employees") }}',
            method: 'GET',
            success: function(response) {
                incentiveEmployees = response.employees || response;
                
                if (!incentiveEmployees || !Array.isArray(incentiveEmployees)) {
                    console.log('Invalid employees data:', response);
                    showIncentiveMessage('Error loading employees', 'danger');
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error:', xhr.responseText);
                showIncentiveMessage('Error loading employees', 'danger');
            }
        });
    }

    // Searchable dropdown functionality for incentive
    $('#incentive_team_member').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        const dropdown = $('#incentiveEmployeeDropdown');
        
        if (searchTerm.length === 0) {
            dropdown.hide();
            $('#incentive_team_member_id').val('');
            return;
        }
        
        const filteredEmployees = incentiveEmployees.filter(employee => 
            employee.name.toLowerCase().includes(searchTerm)
        );
        
        if (filteredEmployees.length > 0) {
            let dropdownHtml = '';
            filteredEmployees.forEach(employee => {
                dropdownHtml += `<a class="dropdown-item incentive-employee-item" href="#" data-id="${employee.id}" data-name="${employee.name}">${employee.name}</a>`;
            });
            dropdown.html(dropdownHtml).show();
        } else {
            dropdown.html('<span class="dropdown-item-text text-muted">No employees found</span>').show();
        }
    });

    // Handle employee selection for incentive
    $(document).on('click', '.incentive-employee-item', function(e) {
        e.preventDefault();
        const employeeId = $(this).data('id');
        const employeeName = $(this).data('name');
        
        $('#incentive_team_member').val(employeeName);
        $('#incentive_team_member_id').val(employeeId);
        $('#incentiveEmployeeDropdown').hide();
    });

    // Hide dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#incentive_team_member, #incentiveEmployeeDropdown').length) {
            $('#incentiveEmployeeDropdown').hide();
        }
    });

    // Clear hidden field when input is manually cleared
    $('#incentive_team_member').on('keyup', function() {
        if ($(this).val() === '') {
            $('#incentive_team_member_id').val('');
        }
    });

    // Date validation - end date should be after start date
    $('#start_date, #end_date').on('change', function() {
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();
        
        if (startDate && endDate && startDate > endDate) {
            showIncentiveMessage('End date must be after start date', 'danger');
            $('#end_date').val('');
        }
    });

    // Submit incentive
    $('#submitIncentive').on('click', function() {
        const formData = {
            incentive_giver_name: $('#incentive_giver_name').val(),
            incentive_team_member_id: $('#incentive_team_member_id').val(),
            incentive_team_member: $('#incentive_team_member').val(),
            incentive_amount: $('#incentive_amount').val(),
            // start_date: $('#start_date').val(),
            end_date: $('#end_date').val(),
            incentive_description: $('#incentive_description').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        // Basic validation
        if (!formData.incentive_team_member_id) {
            showIncentiveMessage('Please select a team member', 'danger');
            return;
        }
        if (!formData.incentive_amount || formData.incentive_amount <= 0) {
            showIncentiveMessage('Please enter a valid incentive amount', 'danger');
            return;
        }
           if (!formData.end_date) { // Only check end date now
        showIncentiveMessage('Please select an end date', 'danger');
        return;
    }
        if (!formData.incentive_description.trim()) {
            showIncentiveMessage('Please provide an incentive description', 'danger');
            return;
        }

        // Submit the incentive
        $.ajax({
            url: '{{ route("incentives.store") }}',
            method: 'POST',
            data: formData,
            beforeSend: function() {
                $('#submitIncentive').prop('disabled', true).text('Submitting...');
            },
            success: function(response) {
                console.log('Success response:', response);
                showIncentiveMessage('Incentive submitted successfully!', 'success');
                setTimeout(function() {
                    $('#incentiveModal').modal('hide');
                    resetIncentiveForm();
                }, 2000);
            },
            error: function(xhr, status, error) {
                console.log('Error response:', xhr);
                console.log('Status:', status);
                console.log('Error:', error);
                
                let errorMessage = 'Error submitting incentive';
                
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        errorMessage = errors.join(', ');
                    }
                } else if (xhr.responseText) {
                    console.log('Response text:', xhr.responseText);
                    errorMessage = 'Server error. Check browser console for details.';
                }
                
                showIncentiveMessage(errorMessage, 'danger');
            },
            complete: function() {
                $('#submitIncentive').prop('disabled', false).text('Give Incentive');
            }
        });
    });

    // Reset incentive form when modal is hidden
    $('#incentiveModal').on('hidden.bs.modal', function() {
        resetIncentiveForm();
    });

    function resetIncentiveForm() {
        $('#incentiveForm')[0].reset();
        $('#incentive_team_member_id').val('');
        $('#incentiveEmployeeDropdown').hide();
        hideIncentiveMessage();
    }

    function showIncentiveMessage(message, type) {
        const messageDiv = $('#incentiveMessage');
        messageDiv.removeClass('d-none alert-success alert-danger alert-info')
                  .addClass(`alert-${type}`)
                  .text(message);
    }

    function hideIncentiveMessage() {
        $('#incentiveMessage').addClass('d-none');
    }
    // END: Incentive Modal Functionality

    // START: Deduction Modal Functionality
    // Load employees for deduction when modal opens
    $('#deductionModal').on('show.bs.modal', function() {
        $.ajax({
            url: '{{ route("reviews.employees") }}',
            method: 'GET',
            success: function(response) {
                deductionEmployees = response.employees || response;
            },
            error: function(xhr) {
                deductionEmployees = [];
            }
        });
    });

    // Deduction Modal: Searchable dropdown
    let deductionEmployees = [];
    $('#deduction_team_member').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        const dropdown = $('#deductionEmployeeDropdown');
        
        if (searchTerm.length === 0) {
            dropdown.hide();
            return;
        }
        
        const filteredEmployees = deductionEmployees.filter(employee => 
            employee.name.toLowerCase().includes(searchTerm)
        );
        
        if (filteredEmployees.length > 0) {
            let dropdownHtml = '';
            filteredEmployees.forEach(employee => {
                dropdownHtml += `<a class="dropdown-item deduction-employee-item" href="#" data-id="${employee.id}" data-name="${employee.name}">${employee.name}</a>`;
            });
            dropdown.html(dropdownHtml).show();
        } else {
            dropdown.html('<span class="dropdown-item-text text-muted">No employees found</span>').show();
        }
    });

    // Handle deduction employee selection
    $(document).on('click', '.deduction-employee-item', function(e) {
        e.preventDefault();
        const employeeId = $(this).data('id');
        const employeeName = $(this).data('name');
        
        $('#deduction_team_member').val(employeeName);
        $('#deduction_team_member_id').val(employeeId);
        $('#deductionEmployeeDropdown').hide();
    });

    // Submit deduction
    $('#submitDeduction').on('click', function() {
        const formData = {
            _token: '{{ csrf_token() }}',
            deduction_giver_name: $('#deduction_giver_name').val(),
            deduction_team_member_id: $('#deduction_team_member_id').val(),
            deduction_team_member: $('#deduction_team_member').val(),
            deduction_amount: $('#deduction_amount').val(),
            deduction_date: $('#deduction_date').val(),
            deduction_description: $('#deduction_description').val()
        };

        // Validate form
        if (!formData.deduction_team_member_id || !formData.deduction_amount || !formData.deduction_date || !formData.deduction_description) {
            showDeductionMessage('Please fill in all required fields.', 'danger');
            return;
        }

        console.log('Submitting deduction:', formData); // Debug log

        $.ajax({
            url: '{{ route("deductions.store") }}',
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/x-www-form-urlencoded',
                'Accept': 'application/json'
            },
            beforeSend: function() {
                $('#submitDeduction').prop('disabled', true).text('Applying...');
            },
            success: function(response) {
                console.log('Deduction success:', response); // Debug log
                if (response.success) {
                    showDeductionMessage(response.message || 'Deduction applied successfully!', 'success');
                    setTimeout(() => {
                        $('#deductionModal').modal('hide');
                        resetDeductionForm();
                    }, 2000);
                } else {
                    showDeductionMessage(response.message || 'Failed to apply deduction.', 'danger');
                }
            },
            error: function(xhr) {
                console.log('Deduction error:', xhr); // Debug log
                console.log('Response text:', xhr.responseText); // Debug log
                let errorMessage = 'An error occurred while applying deduction.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMessage = errors.join(', ');
                } else if (xhr.status === 422) {
                    errorMessage = 'Validation error: Please check all required fields.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Server error: Please contact administrator.';
                } else if (xhr.status === 404) {
                    errorMessage = 'Route not found. Please contact administrator.';
                }
                showDeductionMessage(errorMessage, 'danger');
            },
            complete: function() {
                $('#submitDeduction').prop('disabled', false).text('Apply Deduction');
            }
        });
    });

    // Reset deduction form when modal is hidden
    $('#deductionModal').on('hidden.bs.modal', function() {
        resetDeductionForm();
    });

    function resetDeductionForm() {
        $('#deductionForm')[0].reset();
        $('#deduction_team_member_id').val('');
        $('#deductionEmployeeDropdown').hide();
        $('#deduction_date').val('{{ date("Y-m-d") }}');
        hideDeductionMessage();
    }

    function showDeductionMessage(message, type) {
        const messageDiv = $('#deductionMessage');
        messageDiv.removeClass('d-none alert-success alert-danger alert-info')
                  .addClass(`alert-${type}`)
                  .text(message);
    }

    function hideDeductionMessage() {
        $('#deductionMessage').addClass('d-none');
    }
    // END: Deduction Modal Functionality

    // === DAR Modal Draft Save/Load (per user) ===
$(function() {
    // Use user id or email for unique key
    var userId = "{{ Auth::user()->id }}";
    var draftKey = "tmr_draft_" + userId;

    // Save draft when modal is closed (if not submitted)
    $('#darModal').on('hide.bs.modal', function () {
        if (!$('#darForm').data('submitted')) {
            var draft = {
                report_date: $('#reportDate').val(),
                tasks: []
            };
            $('#taskContainer .task-row').each(function(i, row) {
                draft.tasks.push({
                    group_name: $(row).find('.task-group').val(),
                    description: $(row).find('.task-description').val(),
                    time_spent: $(row).find('.task-time').val(),
                    status: $(row).find('.task-status').val()
                });
            });
            localStorage.setItem(draftKey, JSON.stringify(draft));
        }
    });

    // Load draft when modal is opened
    $('#darModal').on('show.bs.modal', function () {
        $('#darForm').data('submitted', false); // reset
        var draft = localStorage.getItem(draftKey);
        if (draft) {
            draft = JSON.parse(draft);
            $('#reportDate').val(draft.report_date || "{{ date('Y-m-d') }}");
            // Remove all but first row
            $('#taskContainer .task-row:gt(0)').remove();
            // Fill rows
            if (draft.tasks && draft.tasks.length > 0) {
                for (var i = 0; i < draft.tasks.length; i++) {
                    if (i > 0) $('#addTask').click();
                    var row = $('#taskContainer .task-row').eq(i);
                    row.find('.task-group').val(draft.tasks[i].group_name);
                    row.find('.task-description').val(draft.tasks[i].description);
                    row.find('.task-time').val(draft.tasks[i].time_spent);
                    row.find('.task-status').val(draft.tasks[i].status);
                }
            }
            // Trigger change to update total time if needed
            $('#taskContainer .task-time').trigger('input');
        }
    });

    // Clear draft on submit
    $('#submitDAR').on('click', function() {
        $('#darForm').data('submitted', true);
        localStorage.removeItem(draftKey);
        // If you have AJAX submit, call it here. Otherwise, let your existing submit logic run.
    });
});
});
</script>
<!-- Header Apps Submenu JS -->
 <script>
    const submenuMappings = [
        { item: document.getElementById("operationsItem"), menu: document.getElementById("operationsMenu") },
        { item: document.getElementById("incentiveItem"), menu: document.getElementById("incentiveMenu") },
        { item: document.getElementById("improvementsItem"), menu: document.getElementById("improvementsMenu") },
    ];

    // Function to close all submenus
    function closeAllMenus() {
        submenuMappings.forEach(({ menu }) => {
            if (menu) menu.style.display = "none";
        });
    }

    // Add event listeners for each menu item
    submenuMappings.forEach(({ item, menu }) => {
        if (item && menu) {
            item.addEventListener("click", function (e) {
                // Don't prevent default if clicking inside the submenu
                if (menu.contains(e.target)) {
                    return;
                }
                e.stopPropagation();
                e.preventDefault();
                const isOpen = menu.style.display === "block";

                // Close all menus first
                closeAllMenus();

                // Toggle only the clicked one
                menu.style.display = isOpen ? "none" : "block";
            });
        }
    });

    // Stop propagation on submenu links to prevent parent toggle
    submenuMappings.forEach(({ menu }) => {
        if (menu) {
            const links = menu.querySelectorAll('a');
            links.forEach(link => {
                link.addEventListener("click", function(e) {
                    e.stopPropagation();
                });
            });
        }
    });

    // Close submenus when clicking outside
    document.addEventListener("click", function (e) {
        let clickedInsideSubmenu = false;
        submenuMappings.forEach(({ item, menu }) => {
            if ((item && item.contains(e.target)) || (menu && menu.contains(e.target))) {
                clickedInsideSubmenu = true;
            }
        });
        if (!clickedInsideSubmenu) {
            closeAllMenus();
        }
    });
</script>


<script>
    function updateClocks() {
        // Indian Time (only update if element exists)
        const indianClock = document.getElementById('indian-clock');
        if (indianClock) {
            const indiaOptions = { 
                timeZone: 'Asia/Kolkata',
                hour12: true,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            indianClock.textContent = new Date().toLocaleTimeString('en-IN', indiaOptions);
        }

        // California Time
        const californiaClock = document.getElementById('california-clock');
        if (californiaClock) {
            const caliOptions = {
                timeZone: 'America/Los_Angeles',
                hour12: true,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            californiaClock.textContent = new Date().toLocaleTimeString('en-US', caliOptions);
        }
            
        // Ohio Time
        const ohioClock = document.getElementById('ohio-clock');
        if (ohioClock) {
            const ohioOptions = {
                timeZone: 'America/New_York',
                hour12: true,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            ohioClock.textContent = new Date().toLocaleTimeString('en-US', ohioOptions);
        }
            
        // China Time (only update if element exists)
        const chinaClock = document.getElementById('china-clock');
        if (chinaClock) {
            const chinaOptions = {
                timeZone: 'Asia/Shanghai',
                hour12: true,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            chinaClock.textContent = new Date().toLocaleTimeString('en-US', chinaOptions);
        }
    }
    // Update clocks immediately and then every second
    updateClocks();
    setInterval(updateClocks, 500);

    // Incentive Notification System
    $(document).ready(function() {
        function checkIncentives() {
            $.ajax({
                url: '{{ route("check.incentives") }}',
                method: 'GET',
                success: function(response) {
                    console.log('Incentive check response:', response);
                    
                    // Always show header amount if user has ANY active incentives
                    if (response.success && response.hasAnyIncentives && response.totalAmount > 0) {
                        $('#incentiveAmount').text(response.totalAmount.toFixed(2));
                        $('#incentiveAmountDisplay').show();
                    } else {
                        $('#incentiveAmountDisplay').hide();
                    }
                    
                    // Show popup only for NEW incentives
                    if (response.success && response.hasNewIncentives && response.incentives && response.incentives.length > 0) {
                        // Check if popup should be shown (once per session, not per day)
                        const sessionKey = 'incentive_popup_shown_session_' + Date.now();
                        const popupShown = sessionStorage.getItem('incentive_popup_shown');
                        
                        if (!popupShown) {
                            // Show popup for new incentives
                            showIncentivePopup(response.incentives, response.totalAmount);
                            sessionStorage.setItem('incentive_popup_shown', 'true');
                            
                            // Mark notifications as read after showing popup (delay to ensure popup stays)
                            setTimeout(function() {
                                markNotificationsRead();
                            }, 1000);
                        }
                    }
                },
                error: function(xhr) {
                    console.error('Error checking incentives:', xhr.responseText);
                    try {
                        const errorResponse = JSON.parse(xhr.responseText);
                        console.error('Error details:', errorResponse);
                    } catch (e) {
                        console.error('Could not parse error response');
                    }
                }
            });
        }

        function markNotificationsRead() {
            $.ajax({
                url: '{{ route("mark.notification.read") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log('Notifications marked as read');
                },
                error: function(xhr) {
                    console.error('Error marking notifications as read:', xhr.responseText);
                }
            });
        }

        function showIncentivePopup(incentives, totalAmount) {
            let incentiveList = '';
            incentives.forEach(incentive => {
                incentiveList += `<li style="padding: 5px 0;">${incentive.description || 'Incentive'}: $${parseFloat(incentive.amount || 0).toFixed(2)} from ${incentive.giver_name}</li>`;
            });

            const popupHtml = `
                <div id="incentivePopup" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); 
                     background: white; padding: 30px; border-radius: 15px; box-shadow: 0 8px 25px rgba(0,0,0,0.2); 
                     z-index: 10000; border: 3px solid #28a745; max-width: 450px; min-width: 350px;">
                    <div style="text-align: center; color: #28a745; margin-bottom: 20px;">
                        <i class="ti ti-gift" style="font-size: 60px; margin-bottom: 10px;"></i>
                        <h3 style="margin: 10px 0; color: #333; font-size: 24px;">ðŸŽ‰ Congratulations! ðŸŽ‰</h3>
                    </div>
                    <div style="text-align: center; margin-bottom: 20px;">
                        <p style="font-size: 18px; margin: 10px 0; color: #555;">You have received incentives!</p>
                        <ul style="list-style: none; padding: 0; margin: 15px 0; background: #f8f9fa; padding: 15px; border-radius: 8px;">
                            ${incentiveList}
                        </ul>
                        <p style="font-size: 22px; font-weight: bold; color: #28a745; margin: 15px 0; padding: 10px; background: #e8f5e8; border-radius: 8px;">
                            Total Amount: $${totalAmount.toFixed(2)}
                        </p>
                    </div>
                    <div style="text-align: center;">
                        <button onclick="closeIncentivePopup()" style="background: linear-gradient(45deg, #28a745, #20c997); color: white; 
                               border: none; padding: 12px 30px; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: bold;">
                            Awesome! Thanks ðŸŽ‰
                        </button>
                    </div>
                </div>
                <div id="incentiveOverlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
                     background: rgba(0,0,0,0.6); z-index: 9999;"></div>
            `;

            $('body').append(popupHtml);
            
            // Start auto-close timer
            autoClosePopup();
            
            // Play notification sound
            try {
                const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEASD4AAIA+AAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+D0xWQdASBzxvTdjUAFJHfH8N2QQAsUXrTp66hVFApGn+D0xWQdBSBzxvTdjUAFJHfH8N2QQAsUXrTp66hVFApGn+D0xWQdBSBzxvTdjUAFJHfH8N2QQAsUXrTp66hVFApGn+D0xWQdBSBzxvTdjUAFJHfH8N2QQAsUXrTp66hVFApGn+D0xWQdBSBzxvTdjUAFJHfH8N2QQAsUXrTp66hVFApGn+D0xWQdBSBzxvTdjUAFJHfH8N2QQAsUXrTp66hVFApGn+D0xWQdBSBzxvTdjUAFJHfH8N2QQAsUXrTp66hVFApGn+D0xWQdBSBzxvTdjUAFJHfH8N2QQAsUXrTp66hVFApGn+D0xWQdBSBzxvTdjUAF');
                audio.play().catch(e => console.log('Audio play failed:', e));
            } catch (e) {
                console.log('Audio not supported:', e);
            }
        }

        window.closeIncentivePopup = function() {
            $('#incentivePopup').remove();
            $('#incentiveOverlay').remove();
        };

        // Auto-close popup after 10 seconds
        function autoClosePopup() {
            setTimeout(function() {
                if ($('#incentivePopup').length > 0) {
                    closeIncentivePopup();
                }
            }, 10000); // 10 seconds
        }

        // Check for incentives on page load
        checkIncentives();
        
        // Check for incentives every 5 minutes
        setInterval(checkIncentives, 300000);
    });
</script>
<!-- Daily Shipping Checklist Modal -->
<div class="modal fade" id="dailyShippingChecklistModal" tabindex="-1" aria-labelledby="dailyShippingChecklistModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content" style="border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0; padding: 20px;">
                <h5 class="modal-title" id="dailyShippingChecklistModalLabel" style="font-size: 1.5rem; font-weight: 600;">
                    <i class="ti ti-package" style="margin-right: 15px; font-size: 1.8rem;"></i>
                    📦🚚📋 Daily Shipping Operations Re-check ✅🔍🕒
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="font-size: 1.2rem;"></button>
            </div>
            <div class="modal-body" style="padding: 0; background: #f8f9fa;">
                <form id="dailyShippingChecklistForm">
                    @csrf
                    <!-- User Info Section -->
                    <div style="background: #ffffff; padding: 25px; border-bottom: 2px solid #e9ecef;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="user_name" class="form-label fw-bold" style="color: #495057; font-size: 1.1rem;">Name</label>
                                    <input type="text" class="form-control" id="user_name" name="user_name" value="{{ Auth::user()->name }}" readonly style="background: #e9ecef; border: 2px solid #dee2e6; padding: 12px; font-size: 1rem;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="checklist_date" class="form-label fw-bold" style="color: #495057; font-size: 1.1rem;">Date</label>
                                    <input type="date" class="form-control" id="checklist_date" name="checklist_date" value="{{ date('Y-m-d') }}" readonly style="background: #e9ecef; border: 2px solid #dee2e6; padding: 12px; font-size: 1rem;">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Checklist Items -->
                    <div style="padding: 25px; background: #ffffff;">
                        <div class="table-responsive">
                            <table class="table table-hover" style="margin-bottom: 0;">
                                <thead style="background: #495057; color: white;">
                                    <tr>
                                        <th style="width: 45%; padding: 15px; font-size: 1rem; font-weight: 600;">TASK</th>
                                        <th style="width: 25%; text-align: center; padding: 15px; font-size: 1rem; font-weight: 600;">STATUS (YES/NO)</th>
                                        <th style="width: 30%; padding: 15px; font-size: 1rem; font-weight: 600;">COMMENTS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="border-bottom: 2px solid #dee2e6;">
                                        <td style="padding: 20px; font-size: 1rem; color: #495057;">Were all new orders given to Dispatch on time at 3PM?</td>
                                        <td style="text-align: center; padding: 20px;">
                                            <div class="d-flex justify-content-center gap-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="task_1" id="task_1_yes" value="Yes" style="transform: scale(1.3);">
                                                    <label class="form-check-label fw-bold" for="task_1_yes" style="font-size: 1rem; color: #28a745;">Yes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="task_1" id="task_1_no" value="No" style="transform: scale(1.3);">
                                                    <label class="form-check-label fw-bold" for="task_1_no" style="font-size: 1rem; color: #dc3545;">No</label>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding: 20px;">
                                            <textarea class="form-control" name="task_1_comments" placeholder="Enter comments" rows="2" style="border: 2px solid #dee2e6; border-radius: 8px; padding: 10px; font-size: 0.95rem; resize: vertical; min-height: 60px;"></textarea>
                                        </td>
                                    </tr>
                                    <tr style="border-bottom: 2px solid #dee2e6;">
                                        <td style="padding: 20px; font-size: 1rem; color: #495057;">Were any labels printed for cancelled ORDERS Today?</td>
                                        <td style="text-align: center; padding: 20px;">
                                            <div class="d-flex justify-content-center gap-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="task_2" id="task_2_yes" value="Yes" style="transform: scale(1.3);">
                                                    <label class="form-check-label fw-bold" for="task_2_yes" style="font-size: 1rem; color: #28a745;">Yes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="task_2" id="task_2_no" value="No" style="transform: scale(1.3);">
                                                    <label class="form-check-label fw-bold" for="task_2_no" style="font-size: 1rem; color: #dc3545;">No</label>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding: 20px;">
                                            <textarea class="form-control" name="task_2_comments" placeholder="Enter comments" rows="2" style="border: 2px solid #dee2e6; border-radius: 8px; padding: 10px; font-size: 0.95rem; resize: vertical; min-height: 60px;"></textarea>
                                        </td>
                                    </tr>
                                    <tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                                        <td style="padding: 20px; font-size: 1rem; color: #495057;">If yes then - Were cancelled ORDER labels properly voided/refunded?</td>
                                        <td style="text-align: center; padding: 20px;">
                                            <div class="d-flex justify-content-center gap-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="task_3" id="task_3_yes" value="Yes" style="transform: scale(1.3);">
                                                    <label class="form-check-label fw-bold" for="task_3_yes" style="font-size: 1rem; color: #28a745;">Yes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="task_3" id="task_3_no" value="No" style="transform: scale(1.3);">
                                                    <label class="form-check-label fw-bold" for="task_3_no" style="font-size: 1rem; color: #dc3545;">No</label>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding: 20px;">
                                            <textarea class="form-control" name="task_3_comments" placeholder="Enter comments" rows="2" style="border: 2px solid #dee2e6; border-radius: 8px; padding: 10px; font-size: 0.95rem; resize: vertical; min-height: 60px;"></textarea>
                                        </td>
                                    </tr>
                                    <tr style="border-bottom: 2px solid #dee2e6;">
                                        <td style="padding: 20px; font-size: 1rem; color: #495057;">Verified that 20 labels checked randomly were with proper weight and cost?</td>
                                        <td style="text-align: center; padding: 20px;">
                                            <div class="d-flex justify-content-center gap-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="task_4" id="task_4_yes" value="Yes" style="transform: scale(1.3);">
                                                    <label class="form-check-label fw-bold" for="task_4_yes" style="font-size: 1rem; color: #28a745;">Yes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="task_4" id="task_4_no" value="No" style="transform: scale(1.3);">
                                                    <label class="form-check-label fw-bold" for="task_4_no" style="font-size: 1rem; color: #dc3545;">No</label>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding: 20px;">
                                            <textarea class="form-control" name="task_4_comments" placeholder="Enter comments" rows="2" style="border: 2px solid #dee2e6; border-radius: 8px; padding: 10px; font-size: 0.95rem; resize: vertical; min-height: 60px;"></textarea>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="alert alert-info mt-4" role="alert" style="border: none; background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; border-radius: 10px; padding: 20px; font-size: 1rem;">
                            <i class="ti ti-info-circle" style="font-size: 1.3rem; margin-right: 10px;"></i>
                            <strong>Note:</strong> Please ensure all tasks are completed accurately. This checklist helps maintain quality control in our shipping operations.
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="background: #f8f9fa; border-top: 2px solid #dee2e6; padding: 20px; border-radius: 0 0 15px 15px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="padding: 12px 25px; font-size: 1.1rem; border-radius: 8px;">
                    <i class="ti ti-x"></i> Close
                </button>
                <button type="button" class="btn btn-success" id="saveChecklistBtn" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border: none; padding: 12px 25px; font-size: 1.1rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);">
                    <i class="ti ti-check"></i> Save Checklist
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Daily Shipping Checklist Form Submission
document.getElementById('saveChecklistBtn').addEventListener('click', function() {
    const form = document.getElementById('dailyShippingChecklistForm');
    const formData = new FormData(form);
    
    // Validate that all required radio buttons are selected
    const requiredTasks = ['task_1', 'task_2', 'task_4']; // task_3 has default value
    let allValid = true;
    
    requiredTasks.forEach(task => {
        if (!formData.get(task)) {
            allValid = false;
        }
    });
    
    if (!allValid) {
        alert('Please complete all required tasks before submitting.');
        return;
    }
    
    fetch("{{ route('shipping-checklist.store') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            // Close modal first
            const modal = bootstrap.Modal.getInstance(document.getElementById('dailyShippingChecklistModal'));
            modal.hide();
            
            // Reset form
            document.getElementById('dailyShippingChecklistForm').reset();
            document.getElementById('checklist_date').value = '{{ date("Y-m-d") }}';
            document.getElementById('user_name').value = '{{ Auth::user()->name }}';
            
            // Show success message at the top of the page with better styling
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed';
            alertDiv.style.cssText = `
                top: 20px;
                right: 20px;
                z-index: 9999;
                min-width: 350px;
                box-shadow: 0 8px 25px rgba(0,0,0,0.15);
                border: none;
                background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
                color: white;
                border-radius: 10px;
                font-size: 1.1rem;
                font-weight: 500;
            `;
            alertDiv.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="ti ti-check-circle" style="font-size: 1.5rem; margin-right: 15px;"></i>
                    <div>
                        <strong>Success!</strong><br>
                        Daily Shipping Checklist has been saved successfully.
                    </div>
                    <button type="button" class="btn-close btn-close-white ms-3" data-bs-dismiss="alert" style="font-size: 1.2rem;"></button>
                </div>
            `;
            
            // Add to body (not to any specific container)
            document.body.appendChild(alertDiv);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
            
        } else {
            // Show error message
            alert('Failed to save checklist: ' + (data.message || 'Please try again.'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
});
</script>

<!-- DO's Modal (Beautiful Design) -->
<div class="modal fade" id="dosModal" tabindex="-1" aria-labelledby="dosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; border-top-left-radius:16px; border-top-right-radius:16px; padding: 25px;">
                <div style="display:flex; align-items:center; gap:15px;">
                    <span style="font-size:2.5rem;">✅</span>
                    <div>
                        <h4 class="modal-title" id="dosModalLabel" style="margin-bottom:5px; font-weight:600;">Add New DO</h4>
                        <div style="font-size:1rem; color:#e8eaf6; opacity:0.9;">Capture actions that boost your productivity</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter:invert(1); font-size:1.2rem;"></button>
            </div>
            <div class="modal-body" style="padding:30px; background:#f8f9ff;">
                <form id="dosForm">
                    @csrf
                    <!-- What Field -->
                    <div class="mb-4">
                        <label for="dosWhat" class="form-label" style="font-weight:600; color:#2c3e50; font-size:1.1rem; margin-bottom:8px;">
                            <i class="ti ti-target" style="color:#667eea; margin-right:8px;"></i>What to DO? 
                            <span style="color:#7f8c8d;font-size:0.9rem;">(Action title)</span>
                        </label>
                        <input type="text" class="form-control" id="dosWhat" name="dosWhat" 
                               placeholder="e.g., Review emails first thing in the morning" 
                               maxlength="100" required
                               style="border:2px solid #e1e8ed; border-radius:10px; padding:15px; font-size:1rem; transition:all 0.3s;">
                        <small class="text-muted" style="font-size:0.85rem;">
                            <i class="ti ti-info-circle"></i> Give this DO a clear, actionable title (max 100 chars)
                        </small>
                    </div>

                    <!-- Why and How Fields Side by Side -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="dosWhy" class="form-label" style="font-weight:600; color:#2c3e50; font-size:1.1rem; margin-bottom:8px;">
                                <i class="ti ti-bulb" style="color:#f39c12; margin-right:8px;"></i>Why is this important?
                            </label>
                            <textarea class="form-control" id="dosWhy" name="dosWhy" rows="4" 
                                      placeholder="e.g., Helps prioritize urgent tasks and reduces decision fatigue throughout the day" 
                                      required
                                      style="border:2px solid #e1e8ed; border-radius:10px; padding:15px; font-size:0.95rem; resize:vertical; transition:all 0.3s;"></textarea>
                            <small class="text-muted" style="font-size:0.85rem;">
                                <i class="ti ti-arrow-right"></i> Explain the motivation behind this action
                            </small>
                        </div>
                        <div class="col-md-6">
                            <label for="dosImpact" class="form-label" style="font-weight:600; color:#2c3e50; font-size:1.1rem; margin-bottom:8px;">
                                <i class="ti ti-trending-up" style="color:#27ae60; margin-right:8px;"></i>Productivity Impact
                            </label>
                            <textarea class="form-control" id="dosImpact" name="dosImpact" rows="4" 
                                      placeholder="e.g., Saves 45 minutes daily, reduces stress, improves focus for important tasks" 
                                      required
                                      style="border:2px solid #e1e8ed; border-radius:10px; padding:15px; font-size:0.95rem; resize:vertical; transition:all 0.3s;"></textarea>
                            <small class="text-muted" style="font-size:0.85rem;">
                                <i class="ti ti-chart-line"></i> How does this improve your work efficiency?
                            </small>
                        </div>
                    </div>

                    <!-- Priority Level -->
                    <div class="mb-4">
                        <label class="form-label" style="font-weight:600; color:#2c3e50; font-size:1.1rem; margin-bottom:15px;">
                            <i class="ti ti-star" style="color:#e74c3c; margin-right:8px;"></i>Priority Level
                        </label>
                        <div class="d-flex gap-4 flex-wrap">
                            <div class="form-check" style="padding:15px; background:#fff; border:2px solid #e1e8ed; border-radius:10px; min-width:120px; text-align:center; transition:all 0.3s;">
                                <input class="form-check-input" type="radio" name="dosPriority" id="dosHigh" value="High" required style="transform:scale(1.3);">
                                <label class="form-check-label fw-bold" for="dosHigh" style="color:#e74c3c; font-size:1rem; margin-left:8px;">🔥 High</label>
                            </div>
                            <div class="form-check" style="padding:15px; background:#fff; border:2px solid #e1e8ed; border-radius:10px; min-width:120px; text-align:center; transition:all 0.3s;">
                                <input class="form-check-input" type="radio" name="dosPriority" id="dosMedium" value="Medium" required style="transform:scale(1.3);">
                                <label class="form-check-label fw-bold" for="dosMedium" style="color:#f39c12; font-size:1rem; margin-left:8px;">⚡ Medium</label>
                            </div>
                            <div class="form-check" style="padding:15px; background:#fff; border:2px solid #e1e8ed; border-radius:10px; min-width:120px; text-align:center; transition:all 0.3s;">
                                <input class="form-check-input" type="radio" name="dosPriority" id="dosLow" value="Low" required style="transform:scale(1.3);">
                                <label class="form-check-label fw-bold" for="dosLow" style="color:#27ae60; font-size:1rem; margin-left:8px;">📝 Low</label>
                            </div>
                        </div>
                    </div>

                    <!-- Success/Error Messages -->
                    <div id="dosMessage" class="alert d-none" style="border-radius:10px; margin-top:20px;"></div>
                </form>
            </div>
            <div class="modal-footer" style="border-bottom-left-radius:16px; border-bottom-right-radius:16px; padding:25px; background:#f8f9ff; border-top:1px solid #e1e8ed;">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="padding:12px 30px; border-radius:8px; font-weight:500; border:2px solid #6c757d;">
                    <i class="ti ti-x"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary" id="submitDos" style="background:linear-gradient(135deg, #667eea 0%, #764ba2 100%); border:none; padding:12px 30px; border-radius:8px; font-weight:600; box-shadow:0 4px 15px rgba(102, 126, 234, 0.4);">
                    <i class="ti ti-check"></i> Save DO
                </button>
            </div>
        </div>
    </div>
</div>

<!-- DO's Modal JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add hover effects to form elements
    const formControls = document.querySelectorAll('#dosModal .form-control');
    formControls.forEach(control => {
        control.addEventListener('focus', function() {
            this.style.borderColor = '#667eea';
            this.style.boxShadow = '0 0 0 0.2rem rgba(102, 126, 234, 0.25)';
        });
        control.addEventListener('blur', function() {
            this.style.borderColor = '#e1e8ed';
            this.style.boxShadow = 'none';
        });
    });

    // Add hover effects to radio buttons
    const radioContainers = document.querySelectorAll('#dosModal .form-check');
    radioContainers.forEach(container => {
        container.addEventListener('mouseenter', function() {
            this.style.borderColor = '#667eea';
            this.style.backgroundColor = '#f8f9ff';
        });
        container.addEventListener('mouseleave', function() {
            if (!this.querySelector('input').checked) {
                this.style.borderColor = '#e1e8ed';
                this.style.backgroundColor = '#fff';
            }
        });
    });

    // Handle form submission
    document.getElementById('submitDos').addEventListener('click', function() {
        const form = document.getElementById('dosForm');
        const messageDiv = document.getElementById('dosMessage');
        
        // Validate form
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // Get form values
        const dosWhat = document.getElementById('dosWhat').value;
        const dosWhy = document.getElementById('dosWhy').value;
        const dosImpact = document.getElementById('dosImpact').value;
        const dosPriority = document.querySelector('input[name="dosPriority"]:checked')?.value;

        // Show loading state
        this.innerHTML = '<i class="ti ti-loader ti-spin"></i> Saving...';
        this.disabled = true;

        // API call to save DO
        fetch('/dos', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                dosWhat: dosWhat,
                dosWhy: dosWhy,
                dosImpact: dosImpact,
                dosPriority: dosPriority
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Show success message
                messageDiv.className = 'alert alert-success';
                messageDiv.innerHTML = '<i class="ti ti-check-circle"></i> <strong>Success!</strong> Your DO has been saved successfully.';
                messageDiv.classList.remove('d-none');
                
                // Reset form
                form.reset();
                
                // Close modal after 2 seconds
                setTimeout(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('dosModal'));
                    modal.hide();
                    messageDiv.classList.add('d-none');
                }, 2000);
                
            } else {
                // Show error message
                messageDiv.className = 'alert alert-danger';
                messageDiv.innerHTML = '<i class="ti ti-alert-circle"></i> <strong>Error!</strong> ' + (data.message || 'Failed to save. Please try again.');
                messageDiv.classList.remove('d-none');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            messageDiv.className = 'alert alert-danger';
            messageDiv.innerHTML = '<i class="ti ti-alert-circle"></i> <strong>Error!</strong> Network error. Please try again.';
            messageDiv.classList.remove('d-none');
        })
        .finally(() => {
            // Reset button
            this.innerHTML = '<i class="ti ti-check"></i> Save DO';
            this.disabled = false;
        });
    });

    // Reset form when modal is closed
    document.getElementById('dosModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('dosForm').reset();
        document.getElementById('dosMessage').classList.add('d-none');
        
        // Reset radio button containers
        const radioContainers = document.querySelectorAll('#dosModal .form-check');
        radioContainers.forEach(container => {
            container.style.borderColor = '#e1e8ed';
            container.style.backgroundColor = '#fff';
        });
    });
});
</script>

<!-- DON'T Modal (Beautiful Design) -->
<div class="modal fade" id="dontModal" tabindex="-1" aria-labelledby="dontModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: linear-gradient(135deg, #ff512f 0%, #dd2476 100%); color: #fff; border-top-left-radius:16px; border-top-right-radius:16px; padding: 25px;">
                <div style="display:flex; align-items:center; gap:15px;">
                    <span style="font-size:2.5rem;">🚫</span>
                    <div>
                        <h4 class="modal-title" id="dontModalLabel" style="margin-bottom:5px; font-weight:600;">Add New DON'T</h4>
                        <div style="font-size:1rem; color:#ffe0e6; opacity:0.9;">Capture actions to avoid for better productivity</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter:invert(1); font-size:1.2rem;"></button>
            </div>
            <div class="modal-body" style="padding:30px; background:#fff5f5;">
                <form id="dontForm">
                    @csrf
                    <!-- What Field -->
                    <div class="mb-4">
                        <label for="dontWhat" class="form-label" style="font-weight:600; color:#2c3e50; font-size:1.1rem; margin-bottom:8px;">
                            <i class="ti ti-ban" style="color:#ff512f; margin-right:8px;"></i>What NOT to DO? 
                            <span style="color:#7f8c8d;font-size:0.9rem;">(Action to avoid)</span>
                        </label>
                        <input type="text" class="form-control" id="dontWhat" name="dontWhat" 
                               placeholder="e.g., Check social media during work hours" 
                               maxlength="100" required
                               style="border:2px solid #fecaca; border-radius:10px; padding:15px; font-size:1rem; transition:all 0.3s;">
                        <small class="text-muted" style="font-size:0.85rem;">
                            <i class="ti ti-info-circle"></i> Describe the action you should avoid (max 100 chars)
                        </small>
                    </div>

                    <!-- Why and How Fields Side by Side -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="dontWhy" class="form-label" style="font-weight:600; color:#2c3e50; font-size:1.1rem; margin-bottom:8px;">
                                <i class="ti ti-alert-triangle" style="color:#f59e0b; margin-right:8px;"></i>Why avoid this?
                            </label>
                            <textarea class="form-control" id="dontWhy" name="dontWhy" rows="4" 
                                      placeholder="e.g., Breaks concentration, leads to time waste, causes distraction from important tasks" 
                                      required
                                      style="border:2px solid #fecaca; border-radius:10px; padding:15px; font-size:0.95rem; resize:vertical; transition:all 0.3s;"></textarea>
                            <small class="text-muted" style="font-size:0.85rem;">
                                <i class="ti ti-arrow-right"></i> Explain why this action hurts productivity
                            </small>
                        </div>
                        <div class="col-md-6">
                            <label for="dontImpact" class="form-label" style="font-weight:600; color:#2c3e50; font-size:1.1rem; margin-bottom:8px;">
                                <i class="ti ti-trending-down" style="color:#ef4444; margin-right:8px;"></i>Negative Impact
                            </label>
                            <textarea class="form-control" id="dontImpact" name="dontImpact" rows="4" 
                                      placeholder="e.g., Wastes 2+ hours daily, reduces focus by 60%, delays project completion" 
                                      required
                                      style="border:2px solid #fecaca; border-radius:10px; padding:15px; font-size:0.95rem; resize:vertical; transition:all 0.3s;"></textarea>
                            <small class="text-muted" style="font-size:0.85rem;">
                                <i class="ti ti-chart-line-down"></i> How does this harm your work efficiency?
                            </small>
                        </div>
                    </div>

                    <!-- Severity Level -->
                    <div class="mb-4">
                        <label class="form-label" style="font-weight:600; color:#2c3e50; font-size:1.1rem; margin-bottom:15px;">
                            <i class="ti ti-flame" style="color:#ef4444; margin-right:8px;"></i>Severity Level
                        </label>
                        <div class="d-flex gap-4 flex-wrap">
                            <div class="form-check" style="padding:15px; background:#fff; border:2px solid #fecaca; border-radius:10px; min-width:120px; text-align:center; transition:all 0.3s;">
                                <input class="form-check-input" type="radio" name="dontSeverity" id="dontCritical" value="Critical" required style="transform:scale(1.3);">
                                <label class="form-check-label fw-bold" for="dontCritical" style="color:#dc2626; font-size:1rem; margin-left:8px;">🚨 Critical</label>
                            </div>
                            <div class="form-check" style="padding:15px; background:#fff; border:2px solid #fecaca; border-radius:10px; min-width:120px; text-align:center; transition:all 0.3s;">
                                <input class="form-check-input" type="radio" name="dontSeverity" id="dontHigh" value="High" required style="transform:scale(1.3);">
                                <label class="form-check-label fw-bold" for="dontHigh" style="color:#ea580c; font-size:1rem; margin-left:8px;">⚠️ High</label>
                            </div>
                            <div class="form-check" style="padding:15px; background:#fff; border:2px solid #fecaca; border-radius:10px; min-width:120px; text-align:center; transition:all 0.3s;">
                                <input class="form-check-input" type="radio" name="dontSeverity" id="dontMedium" value="Medium" required style="transform:scale(1.3);">
                                <label class="form-check-label fw-bold" for="dontMedium" style="color:#f59e0b; font-size:1rem; margin-left:8px;">🔸 Medium</label>
                            </div>
                        </div>
                    </div>

                    <!-- Success/Error Messages -->
                    <div id="dontMessage" class="alert d-none" style="border-radius:10px; margin-top:20px;"></div>
                </form>
            </div>
            <div class="modal-footer" style="border-bottom-left-radius:16px; border-bottom-right-radius:16px; padding:25px; background:#fff5f5; border-top:1px solid #fecaca;">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="padding:12px 30px; border-radius:8px; font-weight:500; border:2px solid #6c757d;">
                    <i class="ti ti-x"></i> Cancel
                </button>
                <button type="button" class="btn btn-danger" id="submitDont" style="background:linear-gradient(135deg, #ff512f 0%, #dd2476 100%); border:none; padding:12px 30px; border-radius:8px; font-weight:600; box-shadow:0 4px 15px rgba(255, 81, 47, 0.4);">
                    <i class="ti ti-ban"></i> Save DON'T
                </button>
            </div>
        </div>
    </div>
</div>

<!-- DON'T Modal JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add hover effects to form elements
    const dontFormControls = document.querySelectorAll('#dontModal .form-control');
    dontFormControls.forEach(control => {
        control.addEventListener('focus', function() {
            this.style.borderColor = '#ff512f';
            this.style.boxShadow = '0 0 0 0.2rem rgba(255, 81, 47, 0.25)';
        });
        control.addEventListener('blur', function() {
            this.style.borderColor = '#fecaca';
            this.style.boxShadow = 'none';
        });
    });

    // Add hover effects to radio buttons
    const dontRadioContainers = document.querySelectorAll('#dontModal .form-check');
    dontRadioContainers.forEach(container => {
        container.addEventListener('mouseenter', function() {
            this.style.borderColor = '#ff512f';
            this.style.backgroundColor = '#fff5f5';
        });
        container.addEventListener('mouseleave', function() {
            if (!this.querySelector('input').checked) {
                this.style.borderColor = '#fecaca';
                this.style.backgroundColor = '#fff';
            }
        });
    });

    // Handle form submission
    document.getElementById('submitDont').addEventListener('click', function() {
        const form = document.getElementById('dontForm');
        const messageDiv = document.getElementById('dontMessage');
        
        // Validate form
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // Get form values
        const dontWhat = document.getElementById('dontWhat').value;
        const dontWhy = document.getElementById('dontWhy').value;
        const dontImpact = document.getElementById('dontImpact').value;
        const dontSeverity = document.querySelector('input[name="dontSeverity"]:checked')?.value;

        // Show loading state
        this.innerHTML = '<i class="ti ti-loader ti-spin"></i> Saving...';
        this.disabled = true;

        // API call to save DON'T
        fetch('/donts', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                dontWhat: dontWhat,
                dontWhy: dontWhy,
                dontImpact: dontImpact,
                dontSeverity: dontSeverity
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Show success message
                messageDiv.className = 'alert alert-success';
                messageDiv.innerHTML = '<i class="ti ti-check-circle"></i> <strong>Success!</strong> Your DON\'T has been saved successfully.';
                messageDiv.classList.remove('d-none');
                
                // Reset form
                form.reset();
                
                // Close modal after 2 seconds
                setTimeout(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('dontModal'));
                    modal.hide();
                    messageDiv.classList.add('d-none');
                }, 2000);
                
            } else {
                // Show error message
                messageDiv.className = 'alert alert-danger';
                messageDiv.innerHTML = '<i class="ti ti-alert-circle"></i> <strong>Error!</strong> ' + (data.message || 'Failed to save. Please try again.');
                messageDiv.classList.remove('d-none');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            messageDiv.className = 'alert alert-danger';
            messageDiv.innerHTML = '<i class="ti ti-alert-circle"></i> <strong>Error!</strong> Network error. Please try again.';
            messageDiv.classList.remove('d-none');
        })
        .finally(() => {
            // Reset button
            this.innerHTML = '<i class="ti ti-ban"></i> Save DON\'T';
            this.disabled = false;
        });
    });

    // Reset form when modal is closed
    document.getElementById('dontModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('dontForm').reset();
        document.getElementById('dontMessage').classList.add('d-none');
        
        // Reset radio button containers
        const dontRadioContainers = document.querySelectorAll('#dontModal .form-check');
        dontRadioContainers.forEach(container => {
            container.style.borderColor = '#fecaca';
            container.style.backgroundColor = '#fff';
        });
    });

    // ========== SCREENSHOT MODAL FUNCTIONALITY ==========
    
    // Screenshot Modal Variables
    let mediaRecorder = null;
    let recordedChunks = [];
    let stream = null;

    // Take Screenshot Function
    document.getElementById('takeScreenshot').addEventListener('click', async function() {
        try {
            showScreenshotMessage('Taking screenshot...', 'info');
            
            // Request screen capture
            const stream = await navigator.mediaDevices.getDisplayMedia({
                video: { mediaSource: 'screen' }
            });
            
            // Create video element to capture frame
            const video = document.createElement('video');
            video.srcObject = stream;
            video.play();
            
            video.addEventListener('loadedmetadata', function() {
                // Create canvas to capture screenshot
                const canvas = document.createElement('canvas');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                
                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0);
                
                // Convert to blob and show preview
                canvas.toBlob(function(blob) {
                    const url = URL.createObjectURL(blob);
                    document.getElementById('screenshotPreviewImage').src = url;
                    document.getElementById('screenshotPreviewArea').style.display = 'block';
                    hideScreenshotMessage();
                    
                    // Stop the stream
                    stream.getTracks().forEach(track => track.stop());
                });
            });
            
        } catch (err) {
            console.error('Error taking screenshot:', err);
            showScreenshotMessage('Failed to take screenshot. Please try again.', 'danger');
        }
    });

    // Upload Screenshot Function
    document.getElementById('uploadScreenshotBtn').addEventListener('click', function() {
        document.getElementById('screenshotUpload').click();
    });

    document.getElementById('screenshotUpload').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('screenshotPreviewImage').src = e.target.result;
                document.getElementById('screenshotPreviewArea').style.display = 'block';
                showScreenshotMessage('Image uploaded successfully!', 'success');
                setTimeout(() => hideScreenshotMessage(), 2000);
            };
            reader.readAsDataURL(file);
        } else {
            showScreenshotMessage('Please select a valid image file.', 'danger');
        }
    });

    // Screen Recording Functions
    document.getElementById('startRecording').addEventListener('click', async function() {
        try {
            showScreenshotMessage('Starting screen recording...', 'info');
            
            stream = await navigator.mediaDevices.getDisplayMedia({
                video: true,
                audio: true
            });
            
            mediaRecorder = new MediaRecorder(stream);
            recordedChunks = [];
            
            mediaRecorder.ondataavailable = function(event) {
                if (event.data.size > 0) {
                    recordedChunks.push(event.data);
                }
            };
            
            mediaRecorder.onstop = function() {
                const blob = new Blob(recordedChunks, { type: 'video/webm' });
                const url = URL.createObjectURL(blob);
                
                document.getElementById('recordingPreview').src = url;
                document.getElementById('recordingPreviewArea').style.display = 'block';
                hideScreenshotMessage();
            };
            
            mediaRecorder.start();
            
            // Update UI
            document.getElementById('startRecording').classList.add('d-none');
            document.getElementById('stopRecording').classList.remove('d-none');
            document.getElementById('stopRecording').innerHTML = '<span class="recording-indicator"></span> Stop Recording';
            
            showScreenshotMessage('Recording in progress...', 'warning');
            
        } catch (err) {
            console.error('Error starting recording:', err);
            showScreenshotMessage('Failed to start recording. Please try again.', 'danger');
        }
    });

    document.getElementById('stopRecording').addEventListener('click', function() {
        if (mediaRecorder && mediaRecorder.state === 'recording') {
            mediaRecorder.stop();
            
            // Stop all tracks
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
            
            // Update UI
            document.getElementById('startRecording').classList.remove('d-none');
            document.getElementById('stopRecording').classList.add('d-none');
            
            showScreenshotMessage('Recording stopped. Processing...', 'info');
        }
    });

    // Save Screenshot Function
    document.getElementById('saveScreenshot').addEventListener('click', function() {
        const imgSrc = document.getElementById('screenshotPreviewImage').src;
        if (imgSrc) {
            // Create download link
            const link = document.createElement('a');
            link.href = imgSrc;
            link.download = `screenshot-${new Date().getTime()}.png`;
            link.click();
            
            showScreenshotMessage('Screenshot saved successfully!', 'success');
            setTimeout(() => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('screenshotModal'));
                modal.hide();
            }, 1500);
        }
    });

    // Save Recording Function
    document.getElementById('saveRecording').addEventListener('click', function() {
        const videoSrc = document.getElementById('recordingPreview').src;
        if (videoSrc) {
            // Create download link
            const link = document.createElement('a');
            link.href = videoSrc;
            link.download = `screen-recording-${new Date().getTime()}.webm`;
            link.click();
            
            showScreenshotMessage('Recording saved successfully!', 'success');
            setTimeout(() => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('screenshotModal'));
                modal.hide();
            }, 1500);
        }
    });

    // Retake Screenshot
    document.getElementById('retakeScreenshot').addEventListener('click', function() {
        document.getElementById('screenshotPreviewArea').style.display = 'none';
        document.getElementById('screenshotPreviewImage').src = '';
        hideScreenshotMessage();
    });

    // New Recording
    document.getElementById('newRecording').addEventListener('click', function() {
        document.getElementById('recordingPreviewArea').style.display = 'none';
        document.getElementById('recordingPreview').src = '';
        recordedChunks = [];
        hideScreenshotMessage();
    });

    // Screenshot message functions
    function showScreenshotMessage(message, type) {
        const messageDiv = document.getElementById('screenshotMessage');
        messageDiv.className = `alert alert-${type}`;
        messageDiv.innerHTML = `<i class="ti ti-info-circle"></i> ${message}`;
        messageDiv.classList.remove('d-none');
    }

    function hideScreenshotMessage() {
        const messageDiv = document.getElementById('screenshotMessage');
        messageDiv.classList.add('d-none');
    }

    // Reset screenshot modal when closed
    document.getElementById('screenshotModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('screenshotPreviewArea').style.display = 'none';
        document.getElementById('recordingPreviewArea').style.display = 'none';
        document.getElementById('screenshotPreviewImage').src = '';
        document.getElementById('recordingPreview').src = '';
        document.getElementById('screenshotUpload').value = '';
        
        // Reset recording buttons
        document.getElementById('startRecording').classList.remove('d-none');
        document.getElementById('stopRecording').classList.add('d-none');
        
        // Stop any ongoing recording
        if (mediaRecorder && mediaRecorder.state === 'recording') {
            mediaRecorder.stop();
        }
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        
        recordedChunks = [];
        hideScreenshotMessage();
    });

    // ========== END SCREENSHOT MODAL FUNCTIONALITY ==========
});
</script>
