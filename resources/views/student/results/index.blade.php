@extends('student.layouts.app')

@section('title', 'My Results')
@section('page-title', 'My Results')
@section('page-sub', 'Academic performance — ' . ($currentTerm ?? 'First Term'))

@php
$resultsPublished = true;
$selectedTerm     = 'first';

$summary = ['average' => 73.4, 'position' => '5th', 'out_of' => 42, 'highest' => 87.2, 'lowest' => 41.6, 'grade_count' => ['A'=>3,'B'=>5,'C'=>3,'D'=>1,'F'=>0]];

$results = [
    ['subject'=>'Mathematics',       'ca'=>22, 'exam'=>55, 'total'=>77, 'grade'=>'B2', 'position'=>'3rd',  'remark'=>'Good performance'],
    ['subject'=>'English Language',  'ca'=>25, 'exam'=>53, 'total'=>78, 'grade'=>'B2', 'position'=>'4th',  'remark'=>'Very good'],
    ['subject'=>'Basic Science',     'ca'=>20, 'exam'=>62, 'total'=>82, 'grade'=>'B3', 'position'=>'2nd',  'remark'=>'Excellent'],
    ['subject'=>'Social Studies',    'ca'=>18, 'exam'=>50, 'total'=>68, 'grade'=>'C4', 'position'=>'8th',  'remark'=>'Keep it up'],
    ['subject'=>'French',            'ca'=>15, 'exam'=>40, 'total'=>55, 'grade'=>'C5', 'position'=>'14th', 'remark'=>'Needs improvement'],
    ['subject'=>'Computer Studies',  'ca'=>27, 'exam'=>58, 'total'=>85, 'grade'=>'A1', 'position'=>'1st',  'remark'=>'Outstanding'],
    ['subject'=>'Civic Education',   'ca'=>21, 'exam'=>51, 'total'=>72, 'grade'=>'B3', 'position'=>'6th',  'remark'=>'Good'],
    ['subject'=>'Basic Technology',  'ca'=>19, 'exam'=>47, 'total'=>66, 'grade'=>'C4', 'position'=>'9th',  'remark'=>'Fair'],
    ['subject'=>'Agricultural Sci',  'ca'=>23, 'exam'=>54, 'total'=>77, 'grade'=>'B2', 'position'=>'5th',  'remark'=>'Good work'],
    ['subject'=>'PHE',               'ca'=>26, 'exam'=>63, 'total'=>89, 'grade'=>'A1', 'position'=>'1st',  'remark'=>'Excellent'],
    ['subject'=>'Music',             'ca'=>20, 'exam'=>45, 'total'=>65, 'grade'=>'C4', 'position'=>'7th',  'remark'=>'Fair'],
    ['subject'=>'Christian R.K.',    'ca'=>22, 'exam'=>52, 'total'=>74, 'grade'=>'B3', 'position'=>'5th',  'remark'=>'Good'],
];
@endphp

@section('content')

{{-- Term selector --}}
<div class="flex gap-2 flex-wrap mb-5">
  @foreach(['first'=>'First Term','second'=>'Second Term','third'=>'Third Term'] as $key => $label)
    <button onclick="this.closest('.flex').querySelectorAll('button').forEach(b=>{b.classList.remove('bg-forest-700','text-white','border-forest-700');b.classList.add('bg-white','text-gray-500','border-gray-200')});this.classList.add('bg-forest-700','text-white','border-forest-700');this.classList.remove('bg-white','text-gray-500','border-gray-200')"
            class="px-4 py-2 rounded-full border text-sm font-semibold transition-all
                   {{ $key === $selectedTerm ? 'bg-forest-700 text-white border-forest-700' : 'bg-white text-gray-500 border-gray-200 hover:border-forest-600 hover:text-forest-700' }}">
      {{ $label }}
    </button>
  @endforeach
</div>

@if($resultsPublished)
  {{-- Published banner --}}
  <div class="flex flex-wrap items-center justify-between gap-3 p-4 bg-green-50 border border-green-200 rounded-xl mb-5">
    <div class="flex items-center gap-3">
      <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
        <svg class="w-4 h-4 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
      </div>
      <div>
        <p class="text-sm font-semibold text-green-800">Results Published</p>
        <p class="text-xs text-green-600">First Term 2024/2025 results are available</p>
      </div>
    </div>
    <a href="{{ route('student.results.report-card') }}" class="flex items-center gap-1.5 px-4 py-2 bg-forest-700 text-white text-sm font-bold rounded-lg hover:bg-forest-900 transition-colors">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      Download Report Card
    </a>
  </div>
@endif

{{-- Summary band --}}
<div class="bg-white rounded-2xl border border-gray-200 p-5 mb-5">
  <div class="flex flex-wrap items-center gap-5 md:gap-8">

    {{-- Big score --}}
    <div class="text-center flex-shrink-0">
      <p class="font-display font-bold text-5xl text-forest-700 leading-none">{{ $summary['average'] }}</p>
      <p class="text-xs text-gray-400 mt-1">Average Score</p>
    </div>

    <div class="hidden md:block w-px h-16 bg-gray-200"></div>

    {{-- Stats --}}
    <div class="flex flex-wrap gap-5 md:gap-8">
      <div class="text-center">
        <p class="font-display font-bold text-2xl text-gray-900">{{ $summary['position'] }}</p>
        <p class="text-[11px] text-gray-400 uppercase tracking-wider mt-0.5">Position</p>
        <p class="text-xs text-gray-400">of {{ $summary['out_of'] }} students</p>
      </div>
      <div class="text-center">
        <p class="font-display font-bold text-2xl text-gray-900">{{ $summary['highest'] }}</p>
        <p class="text-[11px] text-gray-400 uppercase tracking-wider mt-0.5">Highest</p>
        <p class="text-xs text-gray-400">in class</p>
      </div>
      <div class="text-center">
        <p class="font-display font-bold text-2xl text-gray-900">{{ count($results) }}</p>
        <p class="text-[11px] text-gray-400 uppercase tracking-wider mt-0.5">Subjects</p>
        <p class="text-xs text-gray-400">this term</p>
      </div>
      @foreach($summary['grade_count'] as $g => $cnt)
        @if($cnt > 0)
          <div class="text-center">
            <p class="font-display font-bold text-2xl text-gray-900">{{ $cnt }}</p>
            <p class="text-[11px] text-gray-400 uppercase tracking-wider mt-0.5">{{ $g }} grades</p>
          </div>
        @endif
      @endforeach
    </div>

  </div>
</div>

{{-- Results table --}}
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
  <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2">
    <h3 class="font-display font-semibold text-gray-900">Subject Results</h3>
    <span class="text-xs text-gray-400">(CA = 30 marks · Exam = 70 marks)</span>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full border-collapse">
      <thead>
        <tr class="bg-gray-50 border-b border-gray-200">
          <th class="px-5 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Subject</th>
          <th class="px-4 py-3 text-center text-[11px] font-bold text-gray-500 uppercase tracking-wider">CA/30</th>
          <th class="px-4 py-3 text-center text-[11px] font-bold text-gray-500 uppercase tracking-wider">Exam/70</th>
          <th class="px-4 py-3 text-center text-[11px] font-bold text-gray-500 uppercase tracking-wider">Total</th>
          <th class="px-4 py-3 text-center text-[11px] font-bold text-gray-500 uppercase tracking-wider">Score</th>
          <th class="px-4 py-3 text-center text-[11px] font-bold text-gray-500 uppercase tracking-wider">Grade</th>
          <th class="px-4 py-3 text-center text-[11px] font-bold text-gray-500 uppercase tracking-wider hidden md:table-cell">Position</th>
          <th class="px-4 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Remark</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @foreach($results as $r)
          <tr class="hover:bg-gray-50/50 transition-colors">
            <td class="px-5 py-3.5 text-sm font-semibold text-gray-900 whitespace-nowrap">{{ $r['subject'] }}</td>
            <td class="px-4 py-3.5 text-sm text-gray-600 text-center">{{ $r['ca'] }}</td>
            <td class="px-4 py-3.5 text-sm text-gray-600 text-center">{{ $r['exam'] }}</td>
            <td class="px-4 py-3.5 text-sm font-bold text-gray-900 text-center">{{ $r['total'] }}</td>
            <td class="px-4 py-3.5">
              <div class="flex items-center gap-2 justify-center">
                <div class="score-bar w-16">
                  <div class="score-bar-fill" data-score="{{ $r['total'] }}"></div>
                </div>
                <span class="text-xs font-semibold text-gray-500 w-6">{{ $r['total'] }}</span>
              </div>
            </td>
            <td class="px-4 py-3.5 text-center">
              <span class="grade grade-{{ $r['grade'] }}">{{ $r['grade'] }}</span>
            </td>
            <td class="px-4 py-3.5 text-sm text-gray-500 text-center hidden md:table-cell">{{ $r['position'] }}</td>
            <td class="px-4 py-3.5 text-sm text-gray-400 hidden lg:table-cell">{{ $r['remark'] }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{-- Footer note --}}
  <div class="px-5 py-3 bg-gray-50 border-t border-gray-100">
    <p class="text-xs text-gray-400">
      Nigerian Grading: A1 (75–100) · B2 (70–74) · B3 (65–69) · C4 (60–64) · C5 (55–59) · C6 (50–54) · D7 (45–49) · E8 (40–44) · F9 (0–39)
    </p>
  </div>
</div>

@endsection
