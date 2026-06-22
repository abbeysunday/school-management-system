@extends('student.layouts.app')

@section('title', 'Announcements')
@section('page-title', 'Announcements')
@section('page-sub', 'School news and important notices')

@php
$items = [
    ['id'=>1,'priority'=>'emergency','read'=>false,
     'title'=>'Emergency: Water Supply Disruption Tomorrow',
     'body' =>'Due to critical plumbing works, there will be no water supply from 7 AM to 3 PM tomorrow. All students are advised to come with extra water. Food from the canteen will also be limited. Students with medical needs should inform their class teacher immediately.',
     'author'=>'School Administration','date'=>'4 hours ago'],

    ['id'=>2,'priority'=>'important','read'=>false,
     'title'=>'PTA Meeting — Saturday 26th October 2024',
     'body' =>'There will be a PTA meeting this Saturday, 26th October 2024 at 10:00 AM in the school hall. Agenda includes the first term examination timetable, school fees for next session, and infrastructure updates. All parents are strongly encouraged to attend.',
     'author'=>'Mrs. Adunola Fashola (PTA President)','date'=>'1 day ago'],

    ['id'=>3,'priority'=>'important','read'=>false,
     'title'=>'First Term Mid-Term Examination Timetable Released',
     'body' =>'The timetable for the first term mid-term examinations is now available. Examinations begin Monday, 4th November 2024 and run through Friday, 8th November. CBT exams for Mathematics, English, and Basic Science will be conducted in the ICT lab in groups. Physical timetables are on all notice boards.',
     'author'=>'Academic Affairs Office','date'=>'2 days ago'],

    ['id'=>4,'priority'=>'normal','read'=>true,
     'title'=>'Inter-House Sports Day Rescheduled to 8th November',
     'body' =>'The inter-house sports day originally scheduled for this Friday has been moved to Friday, 8th November 2024 due to the weather forecast. All house captains should ensure their members are prepared. Attendance is compulsory. Students should wear their house colours.',
     'author'=>'Sports Director','date'=>'3 days ago'],

    ['id'=>5,'priority'=>'normal','read'=>true,
     'title'=>'Library Opening Hours Extended',
     'body' =>'Starting this week, the school library will remain open until 5:00 PM on weekdays to allow students prepare for upcoming examinations. Students wishing to use the library after school must sign in at the library desk.',
     'author'=>'Library Department','date'=>'5 days ago'],

    ['id'=>6,'priority'=>'normal','read'=>true,
     'title'=>'School Fees Reminder — Second Instalment Due',
     'body' =>'The second instalment of first term school fees is due by Friday, 1st November 2024. Parents who have not paid are advised to do so promptly to avoid their ward being sent home. Payment can be made via bank transfer or at the school bursary.',
     'author'=>'Bursary Department','date'=>'1 week ago'],
];

$unreadCount = collect($items)->where('read', false)->count();

$priorityMeta = [
    'emergency' => ['icon_color'=>'text-red-600','bg'=>'bg-red-100','border'=>'border-l-red-500','pill'=>'bg-red-100 text-red-700','label'=>'Emergency'],
    'important' => ['icon_color'=>'text-yellow-600','bg'=>'bg-yellow-100','border'=>'border-l-yellow-500','pill'=>'bg-yellow-100 text-yellow-700','label'=>'Important'],
    'normal'    => ['icon_color'=>'text-forest-700','bg'=>'bg-green-100','border'=>'border-l-forest-600','pill'=>'bg-green-100 text-green-700','label'=>'Notice'],
];
@endphp

@section('content')

{{-- Header row --}}
<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
  <p class="text-sm text-gray-500">
    <strong class="text-gray-900">{{ count($items) }}</strong> announcements
    @if($unreadCount > 0)
      · <span class="text-forest-700 font-semibold">{{ $unreadCount }} unread</span>
    @endif
  </p>
  @if($unreadCount > 0)
    <button onclick="markAllRead()" class="flex items-center gap-1.5 px-4 py-2 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
      Mark All as Read
    </button>
  @endif
</div>

{{-- Filter tabs --}}
<div class="flex gap-2 flex-wrap mb-5" data-filter-group="announcements">
  @foreach(['all'=>'All','emergency'=>'Emergency','important'=>'Important','normal'=>'Notice'] as $key => $label)
    <button data-filter="{{ $key }}"
            class="px-4 py-2 rounded-full border text-sm font-semibold transition-all
                   {{ $key==='all' ? 'bg-forest-700 text-white border-forest-700' : 'bg-white text-gray-500 border-gray-200 hover:border-forest-600 hover:text-forest-700' }}">
      {{ $label }}
    </button>
  @endforeach
</div>

{{-- Announcement list --}}
<div class="flex flex-col gap-3">

  @foreach($items as $ann)
    @php $meta = $priorityMeta[$ann['priority']]; @endphp
    <div data-ann-card
         data-filter-target="announcements"
         data-category="{{ $ann['priority'] }}"
         class="bg-white rounded-2xl border border-gray-200 border-l-4 {{ $meta['border'] }} p-4 md:p-5 hover:shadow-sm transition-all {{ $ann['read'] ? 'opacity-70' : '' }}">

      <div class="flex items-start gap-3">

        {{-- Priority icon --}}
        <div class="w-9 h-9 rounded-xl {{ $meta['bg'] }} flex items-center justify-center flex-shrink-0 mt-0.5">
          @if($ann['priority'] === 'emergency')
            <svg class="w-4 h-4 {{ $meta['icon_color'] }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          @elseif($ann['priority'] === 'important')
            <svg class="w-4 h-4 {{ $meta['icon_color'] }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
          @else
            <svg class="w-4 h-4 {{ $meta['icon_color'] }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 17H2a3 3 0 0 0 3-3V9a7 7 0 0 1 14 0v5a3 3 0 0 0 3 3zm-8.27 4a2 2 0 0 1-3.46 0"/></svg>
          @endif
        </div>

        <div class="flex-1 min-w-0">
          {{-- Title row --}}
          <div class="flex items-start justify-between gap-2 mb-1.5">
            <h3 class="text-sm font-bold text-gray-900 leading-snug flex items-center gap-1.5">
              @if(!$ann['read'])
                <span data-unread-dot class="w-2 h-2 bg-forest-600 rounded-full flex-shrink-0 mt-0.5"></span>
              @endif
              {{ $ann['title'] }}
            </h3>
            <span class="text-[11px] font-bold px-2 py-0.5 rounded-full {{ $meta['pill'] }} whitespace-nowrap flex-shrink-0">{{ $meta['label'] }}</span>
          </div>

          {{-- Author + date --}}
          <p class="text-xs text-gray-400 mb-2.5">{{ $ann['author'] }} · {{ $ann['date'] }}</p>

          {{-- Body --}}
          <p class="text-sm text-gray-600 leading-relaxed">{{ $ann['body'] }}</p>

          {{-- Footer --}}
          @if(!$ann['read'])
            <div class="mt-3 pt-3 border-t border-gray-100">
              <button data-mark-read-btn
                      onclick="markRead(this, {{ $ann['id'] }})"
                      class="text-xs font-semibold text-forest-700 hover:text-gold-600 transition-colors">
                Mark as Read
              </button>
            </div>
          @else
            <div class="mt-3 pt-3 border-t border-gray-100">
              <span class="text-xs text-gray-300">Read</span>
            </div>
          @endif
        </div>

      </div>
    </div>
  @endforeach

</div>

@endsection
