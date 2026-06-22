@extends('student.layouts.app')

@section('title', 'CBT Exams')
@section('page-title', 'CBT Exams')
@section('page-sub', 'Computer-Based Tests for ' . ($currentTerm ?? 'this term'))

@php
$exams = [
    ['id'=>1,'subject'=>'Mathematics',      'title'=>'Mathematics Mid-Term CBT',       'date'=>'Mon, 4 Nov 2024','time'=>'10:00 AM','duration'=>45,'questions'=>40,'status'=>'upcoming','score'=>null],
    ['id'=>2,'subject'=>'English Language', 'title'=>'English Language Practice Test', 'date'=>'Wed, 6 Nov 2024','time'=>'9:00 AM', 'duration'=>30,'questions'=>30,'status'=>'upcoming','score'=>null],
    ['id'=>3,'subject'=>'Basic Science',    'title'=>'Basic Science Quiz',             'date'=>'Fri, 8 Nov 2024','time'=>'11:00 AM','duration'=>30,'questions'=>25,'status'=>'upcoming','score'=>null],
    ['id'=>4,'subject'=>'Computer Studies', 'title'=>'Computer Studies End of Term',   'date'=>'Now',            'time'=>'Now',      'duration'=>45,'questions'=>40,'status'=>'active', 'score'=>null],
    ['id'=>5,'subject'=>'Social Studies',   'title'=>'Social Studies Mid-Term',        'date'=>'Oct 14, 2024',   'time'=>'10:00 AM','duration'=>30,'questions'=>30,'status'=>'completed','score'=>78],
    ['id'=>6,'subject'=>'PHE',              'title'=>'PHE Theory Test',                'date'=>'Oct 10, 2024',   'time'=>'9:00 AM', 'duration'=>20,'questions'=>20,'status'=>'completed','score'=>91],
];

$statusMeta = [
    'upcoming'  => ['label'=>'Upcoming',  'pill'=>'bg-blue-100 text-blue-700',   'dot'=>'bg-blue-500'],
    'active'    => ['label'=>'Active',    'pill'=>'bg-green-100 text-green-700', 'dot'=>'bg-green-500 dot-pulse'],
    'completed' => ['label'=>'Completed', 'pill'=>'bg-gray-100 text-gray-600',   'dot'=>'bg-gray-400'],
];
@endphp

@section('content')

{{-- Active exam banner --}}
@php $activeExam = collect($exams)->firstWhere('status','active'); @endphp
@if($activeExam)
  <div class="flex flex-wrap items-center justify-between gap-3 p-4 md:p-5 bg-green-50 border border-green-200 rounded-2xl mb-5">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
        <svg class="w-5 h-5 text-green-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      </div>
      <div>
        <p class="text-sm font-bold text-green-900">Exam In Progress: {{ $activeExam['title'] }}</p>
        <p class="text-xs text-green-600 mt-0.5">{{ $activeExam['questions'] }} questions · {{ $activeExam['duration'] }} minutes</p>
      </div>
    </div>
    <a href="{{ route('student.exams.lobby', $activeExam['id']) }}" class="flex items-center gap-1.5 px-5 py-2.5 bg-green-700 text-white text-sm font-bold rounded-xl hover:bg-green-800 transition-colors">
      Enter Exam →
    </a>
  </div>
@endif

{{-- Filter pills --}}
<div class="flex gap-2 flex-wrap mb-5" data-filter-group="exams">
  @foreach(['all'=>'All Exams','upcoming'=>'Upcoming','active'=>'Active','completed'=>'Completed'] as $key=>$label)
    <button data-filter="{{ $key }}"
            class="px-4 py-2 rounded-full border text-sm font-semibold transition-all
                   {{ $key==='all' ? 'bg-forest-700 text-white border-forest-700' : 'bg-white text-gray-500 border-gray-200 hover:border-forest-600 hover:text-forest-700' }}">
      {{ $label }}
    </button>
  @endforeach
</div>

{{-- Exam cards grid --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
  @foreach($exams as $exam)
    @php $meta = $statusMeta[$exam['status']]; @endphp
    <div data-filter-target="exams" data-category="{{ $exam['status'] }}"
         class="bg-white rounded-2xl border border-gray-200 overflow-hidden hover:shadow-md transition-all hover:border-gray-300 flex flex-col">

      {{-- Card top accent --}}
      <div class="h-1 bg-gradient-to-r {{ $exam['status']==='active' ? 'from-green-500 to-emerald-400' : ($exam['status']==='completed' ? 'from-gray-300 to-gray-200' : 'from-forest-700 to-forest-500') }}"></div>

      <div class="p-5 flex flex-col flex-1">
        {{-- Status badge --}}
        <div class="flex items-center justify-between mb-3">
          <span class="inline-flex items-center gap-1.5 text-xs font-bold px-2.5 py-1 rounded-full {{ $meta['pill'] }}">
            <span class="w-1.5 h-1.5 rounded-full {{ $meta['dot'] }}"></span>
            {{ $meta['label'] }}
          </span>
          <span class="text-xs font-semibold text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">{{ $exam['subject'] }}</span>
        </div>

        {{-- Title --}}
        <h3 class="font-display font-semibold text-gray-900 text-base leading-snug mb-3">{{ $exam['title'] }}</h3>

        {{-- Info grid --}}
        <div class="grid grid-cols-3 gap-2 mb-4">
          <div class="bg-gray-50 rounded-lg p-2 text-center">
            <p class="text-xs font-bold text-gray-900">{{ $exam['duration'] }}m</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Duration</p>
          </div>
          <div class="bg-gray-50 rounded-lg p-2 text-center">
            <p class="text-xs font-bold text-gray-900">{{ $exam['questions'] }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Questions</p>
          </div>
          <div class="bg-gray-50 rounded-lg p-2 text-center">
            @if($exam['status'] === 'completed')
              <p class="text-xs font-bold {{ $exam['score'] >= 75 ? 'text-green-700' : ($exam['score'] >= 50 ? 'text-yellow-700' : 'text-red-600') }}">{{ $exam['score'] }}%</p>
              <p class="text-[10px] text-gray-400 mt-0.5">Score</p>
            @else
              <p class="text-xs font-bold text-gray-900">{{ $exam['time'] }}</p>
              <p class="text-[10px] text-gray-400 mt-0.5">Time</p>
            @endif
          </div>
        </div>

        {{-- Date --}}
        <p class="text-xs text-gray-400 mb-4">
          <span class="mr-1">📅</span>{{ $exam['date'] }}
        </p>

        {{-- Score bar (completed only) --}}
        @if($exam['status'] === 'completed' && $exam['score'])
          <div class="mb-4">
            <div class="score-bar">
              <div class="score-bar-fill" data-score="{{ $exam['score'] }}"></div>
            </div>
          </div>
        @endif

        {{-- CTA --}}
        <div class="mt-auto">
          @if($exam['status'] === 'upcoming')
            <a href="{{ route('student.exams.lobby', $exam['id']) }}"
               class="flex items-center justify-center gap-2 w-full py-2.5 border-2 border-forest-300 text-forest-700 text-sm font-bold rounded-xl hover:bg-forest-700 hover:text-white hover:border-forest-700 transition-all">
              View Lobby
            </a>
          @elseif($exam['status'] === 'active')
            <a href="{{ route('student.exams.lobby', $exam['id']) }}"
               class="flex items-center justify-center gap-2 w-full py-2.5 bg-green-700 text-white text-sm font-bold rounded-xl hover:bg-green-800 transition-colors">
              Enter Exam →
            </a>
          @else
            <button disabled class="flex items-center justify-center gap-2 w-full py-2.5 bg-gray-100 text-gray-400 text-sm font-bold rounded-xl cursor-not-allowed">
              Completed
            </button>
          @endif
        </div>
      </div>

    </div>
  @endforeach
</div>

@endsection
