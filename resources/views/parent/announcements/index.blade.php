@extends('parent.layouts.app')

@section('title', 'Announcements')
@section('page-title', 'Announcements')
@section('page-sub', 'School news and notices for parents')

@php
$items = [
    ['id'=>1,'priority'=>'emergency','read'=>false, 'targeted_to'=>'All Parents',
     'title'=>'Emergency: Water Supply Disruption Tomorrow',
     'body' =>'Due to critical plumbing maintenance works, there will be no water supply from 7 AM to 3 PM tomorrow. All students are advised to come with extra water. Canteen services will be limited. Students with medical needs should inform their class teacher immediately. Parents should make alternative arrangements for their children.',
     'author'=>'School Administration','date'=>'4 hours ago'],

    ['id'=>2,'priority'=>'important','read'=>false,'targeted_to'=>'All Parents',
     'title'=>'PTA Meeting — Saturday 26th October 2024',
     'body' =>'There will be a PTA meeting on Saturday 26th October 2024 at 10:00 AM in the school assembly hall. The agenda includes first term examination timetable, school fees review for next session, and proposed infrastructure updates. Your presence is strongly requested.',
     'author'=>'Mrs. Adunola Fashola (PTA President)','date'=>'1 day ago'],

    ['id'=>3,'priority'=>'important','read'=>false,'targeted_to'=>'JSS 1 & 2 Parents',
     'title'=>'First Term Mid-Term Examination Timetable Released',
     'body' =>'The timetable for first term mid-term exams is now available on the school portal and all notice boards. Examinations begin Monday 4th November 2024. CBT exams for Mathematics, English, and Basic Science will be in the ICT lab. Please remind your ward to prepare adequately.',
     'author'=>'Academic Affairs Office','date'=>'2 days ago'],

    ['id'=>4,'priority'=>'normal','read'=>true,'targeted_to'=>'SS 1 Parents',
     'title'=>'WAEC & NECO Registration — Documents Required',
     'body' =>'SS1 students will begin WAEC and NECO pre-registration this term. Parents of SS1 students should submit the following by Friday 1st November: Birth certificate, passport photographs (×6), and previous school results. Kindly visit the examination office.',
     'author'=>'Examinations Office','date'=>'3 days ago'],

    ['id'=>5,'priority'=>'normal','read'=>true,'targeted_to'=>'All Parents',
     'title'=>'School Fees — Second Instalment Due 1st November',
     'body' =>'The second instalment of first term school fees is due by Friday 1st November 2024. You can pay online through the parent portal or visit the school bursary. Late payments may attract a penalty fee. Please log in to the portal to check your outstanding balance.',
     'author'=>'Bursary Department','date'=>'5 days ago'],

    ['id'=>6,'priority'=>'normal','read'=>true,'targeted_to'=>'All Parents',
     'title'=>'Inter-House Sports Day Rescheduled to 8th November',
     'body' =>'The inter-house sports day has been rescheduled from 25th October to Friday 8th November 2024 due to weather concerns. All students must wear their house colours. Attendance is compulsory. Parents are welcome to attend.',
     'author'=>'Sports Director','date'=>'1 week ago'],
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

{{-- Filters --}}
<div class="flex gap-2 flex-wrap mb-5" data-filter-group="announcements">
  @foreach(['all'=>'All','emergency'=>'Emergency','important'=>'Important','normal'=>'Notice'] as $k=>$l)
    <button data-filter="{{ $k }}"
            class="px-4 py-2 rounded-full border text-sm font-semibold transition-all
                   {{ $k==='all'?'bg-forest-700 text-white border-forest-700':'bg-white text-gray-500 border-gray-200 hover:border-forest-600 hover:text-forest-700' }}">
      {{ $l }}
    </button>
  @endforeach
</div>

{{-- Announcement cards --}}
<div class="flex flex-col gap-3">
  @foreach($items as $ann)
    @php $meta = $priorityMeta[$ann['priority']]; @endphp
    <div data-ann-card
         data-ann-id="{{ $ann['id'] }}"
         data-filter-target="announcements"
         data-category="{{ $ann['priority'] }}"
         class="bg-white rounded-2xl border border-gray-200 border-l-4 {{ $meta['border'] }} overflow-hidden transition-all hover:shadow-sm {{ $ann['read'] ? 'opacity-70' : '' }}">

      {{-- Collapsed header (always visible) --}}
      <div class="flex items-start gap-3 p-4 md:p-5">

        {{-- Icon --}}
        <div class="w-9 h-9 rounded-xl {{ $meta['bg'] }} flex items-center justify-center flex-shrink-0 mt-0.5">
          @if($ann['priority']==='emergency')
            <svg class="w-4 h-4 {{ $meta['icon_color'] }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          @elseif($ann['priority']==='important')
            <svg class="w-4 h-4 {{ $meta['icon_color'] }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/></svg>
          @else
            <svg class="w-4 h-4 {{ $meta['icon_color'] }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 17H2a3 3 0 0 0 3-3V9a7 7 0 0 1 14 0v5a3 3 0 0 0 3 3zm-8.27 4a2 2 0 0 1-3.46 0"/></svg>
          @endif
        </div>

        <div class="flex-1 min-w-0">
          <div class="flex items-start justify-between gap-2 mb-1">
            <div class="flex items-center gap-1.5 flex-1 min-w-0">
              @if(!$ann['read'])
                <span data-unread-dot class="w-2 h-2 bg-forest-600 rounded-full flex-shrink-0"></span>
              @endif
              <h3 class="text-sm font-bold text-gray-900 leading-snug">{{ $ann['title'] }}</h3>
            </div>
            <span class="text-[11px] font-bold px-2 py-0.5 rounded-full {{ $meta['pill'] }} whitespace-nowrap flex-shrink-0">{{ $meta['label'] }}</span>
          </div>

          <div class="flex flex-wrap items-center gap-2 mb-2">
            <p class="text-xs text-gray-400">{{ $ann['author'] }} · {{ $ann['date'] }}</p>
            <span class="text-[10px] font-semibold text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">{{ $ann['targeted_to'] }}</span>
          </div>

          {{-- Body (collapsed by default for long ones, show first 120 chars) --}}
          <p class="text-sm text-gray-600 leading-relaxed">
            {{ Str::limit($ann['body'], 120) }}
          </p>

          {{-- Full body (hidden, toggled) --}}
          <div data-ann-body class="hidden mt-2">
            <p class="text-sm text-gray-600 leading-relaxed">{{ $ann['body'] }}</p>
          </div>

          {{-- Footer --}}
          <div class="flex items-center gap-4 mt-3 pt-3 border-t border-gray-100">
            <button data-ann-toggle class="text-xs font-semibold text-forest-700 hover:text-gold-600 transition-colors">
              Read more →
            </button>
            @if(!$ann['read'])
              <button data-mark-read-btn
                      onclick="markRead(this, {{ $ann['id'] }})"
                      class="text-xs font-semibold text-gray-400 hover:text-gray-600 transition-colors">
                Mark as Read
              </button>
            @else
              <span class="text-xs text-gray-300">Read</span>
            @endif
          </div>
        </div>

      </div>
    </div>
  @endforeach
</div>

@endsection
