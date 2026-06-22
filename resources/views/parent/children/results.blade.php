@extends('parent.layouts.app')

@section('title', 'Results')
@section('page-title', 'Results & Report Cards')
@section('page-sub', 'Published academic results for your children')

@php
$parent   = $parent   ?? ['full_name'=>'Mr. Emmanuel Okafor','first_name'=>'Emmanuel','email'=>'e.okafor@gmail.com','phone'=>'08087654321','photo'=>null];
$children = $children ?? [
    ['id'=>1,'name'=>'Chidinma Okafor','first_name'=>'Chidinma','class'=>'JSS 2B','admission_no'=>'EXC/JSS2/2024/047','gender'=>'Female','photo'=>null,'attendance_pct'=>92,'fee_balance'=>35000,'last_avg'=>73.4,'position'=>'5th'],
    ['id'=>2,'name'=>'Emeka Okafor',   'first_name'=>'Emeka',   'class'=>'SS 1A', 'admission_no'=>'EXC/SS1/2023/019','gender'=>'Male',  'photo'=>null,'attendance_pct'=>88,'fee_balance'=>0,    'last_avg'=>81.2,'position'=>'3rd'],
];

$childId = $childId ?? 1;

$allResults = [
    1 => [
        'child' => ['name'=>'Chidinma Okafor','class'=>'JSS 2B','admission_no'=>'EXC/JSS2/2024/047'],
        'terms'  => [
            'first' => [
                'label'     => 'First Term 2024/2025',
                'published' => true,
                'summary'   => ['average'=>73.4,'position'=>'5th','out_of'=>42],
                'subjects'  => [
                    ['subject'=>'Mathematics',      'ca'=>22,'exam'=>55,'total'=>77,'grade'=>'B2'],
                    ['subject'=>'English Language', 'ca'=>25,'exam'=>53,'total'=>78,'grade'=>'B2'],
                    ['subject'=>'Basic Science',    'ca'=>20,'exam'=>62,'total'=>82,'grade'=>'B3'],
                    ['subject'=>'Social Studies',   'ca'=>18,'exam'=>50,'total'=>68,'grade'=>'C4'],
                    ['subject'=>'French',           'ca'=>15,'exam'=>40,'total'=>55,'grade'=>'C5'],
                    ['subject'=>'Computer Studies', 'ca'=>27,'exam'=>58,'total'=>85,'grade'=>'A1'],
                    ['subject'=>'Civic Education',  'ca'=>21,'exam'=>51,'total'=>72,'grade'=>'B3'],
                    ['subject'=>'Basic Technology', 'ca'=>19,'exam'=>47,'total'=>66,'grade'=>'C4'],
                    ['subject'=>'Agricultural Sci', 'ca'=>23,'exam'=>54,'total'=>77,'grade'=>'B2'],
                    ['subject'=>'PHE',              'ca'=>26,'exam'=>63,'total'=>89,'grade'=>'A1'],
                ],
            ],
            'second' => ['label'=>'Second Term 2024/2025','published'=>false,'summary'=>[],'subjects'=>[]],
            'third'  => ['label'=>'Third Term 2024/2025', 'published'=>false,'summary'=>[],'subjects'=>[]],
        ],
    ],
    2 => [
        'child' => ['name'=>'Emeka Okafor','class'=>'SS 1A','admission_no'=>'EXC/SS1/2023/019'],
        'terms'  => [
            'first' => [
                'label'     => 'First Term 2024/2025',
                'published' => true,
                'summary'   => ['average'=>81.2,'position'=>'3rd','out_of'=>38],
                'subjects'  => [
                    ['subject'=>'Mathematics',      'ca'=>28,'exam'=>62,'total'=>90,'grade'=>'A1'],
                    ['subject'=>'English Language', 'ca'=>26,'exam'=>57,'total'=>83,'grade'=>'B3'],
                    ['subject'=>'Physics',          'ca'=>24,'exam'=>58,'total'=>82,'grade'=>'B3'],
                    ['subject'=>'Chemistry',        'ca'=>22,'exam'=>55,'total'=>77,'grade'=>'B2'],
                    ['subject'=>'Biology',          'ca'=>25,'exam'=>60,'total'=>85,'grade'=>'A1'],
                    ['subject'=>'Further Maths',    'ca'=>20,'exam'=>52,'total'=>72,'grade'=>'B3'],
                    ['subject'=>'Economics',        'ca'=>23,'exam'=>57,'total'=>80,'grade'=>'B2'],
                ],
            ],
            'second' => ['label'=>'Second Term 2024/2025','published'=>false,'summary'=>[],'subjects'=>[]],
            'third'  => ['label'=>'Third Term 2024/2025', 'published'=>false,'summary'=>[],'subjects'=>[]],
        ],
    ],
];

$data = $allResults[$childId] ?? $allResults[1];
@endphp

@section('content')

{{-- Child switcher --}}
@if(count($children) > 1)
  <div class="flex gap-2 flex-wrap mb-5 overflow-x-auto pb-1">
    @foreach($children as $child)
      <a href="{{ route('parent.children.results', $child['id']) }}"
         class="child-tab {{ $child['id'] == $childId ? 'active' : '' }}">
        <div class="child-tab-avatar">{{ strtoupper(substr($child['first_name'],0,1)) }}</div>
        <span class="child-tab-name">{{ $child['first_name'] }}</span>
      </a>
    @endforeach
  </div>
@endif

{{-- Child header --}}
<div class="flex flex-wrap items-center gap-3 mb-5">
  <div class="w-12 h-12 rounded-full border-4 border-gray-100 bg-forest-900 text-white flex items-center justify-content-center font-display font-bold text-lg flex-shrink-0" style="display:flex;align-items:center;justify-content:center">
    {{ strtoupper(substr($data['child']['name'],0,1)) }}
  </div>
  <div>
    <h2 class="font-display font-semibold text-gray-900">{{ $data['child']['name'] }}</h2>
    <p class="text-sm text-gray-400">{{ $data['child']['admission_no'] }} · {{ $data['child']['class'] }}</p>
  </div>
</div>

{{-- Term tabs --}}
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden" data-tab-scope="results">

  <div class="flex border-b border-gray-200 overflow-x-auto" data-tab-nav>
    @foreach($data['terms'] as $termKey => $term)
      <button data-tab="{{ $termKey }}"
              class="px-4 md:px-6 py-4 text-sm font-semibold whitespace-nowrap border-b-2 transition-all flex-shrink-0 flex items-center gap-1.5
                     {{ $termKey==='first' ? 'text-forest-700 border-forest-700' : 'text-gray-400 border-transparent hover:text-gray-600' }}">
        @if(!$term['published'])
          <svg class="w-3.5 h-3.5 opacity-50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        @endif
        {{ str_replace(' 2024/2025','',$term['label']) }}
      </button>
    @endforeach
  </div>

  @foreach($data['terms'] as $termKey => $term)
    <div data-tab-pane="{{ $termKey }}" class="{{ $termKey!=='first'?'hidden':'' }}">

      @if(!$term['published'])
        <div class="p-8 text-center">
          <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3">
            <svg class="w-7 h-7 text-gray-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          </div>
          <p class="font-display font-semibold text-gray-400 text-lg mb-1">Results Not Yet Published</p>
          <p class="text-sm text-gray-400">{{ $term['label'] }} results will appear here once published by the school.</p>
        </div>

      @else
        <div class="p-5 md:p-6">

          {{-- Summary + Download --}}
          <div class="flex flex-wrap items-center gap-4 p-4 bg-forest-50 border border-forest-200 rounded-xl mb-5">
            <div class="flex gap-6 flex-wrap flex-1">
              <div class="text-center">
                <p class="font-display font-bold text-3xl text-forest-800">{{ $term['summary']['average'] }}</p>
                <p class="text-xs text-forest-600 mt-0.5">Average</p>
              </div>
              <div class="text-center">
                <p class="font-display font-bold text-3xl text-forest-800">{{ $term['summary']['position'] }}</p>
                <p class="text-xs text-forest-600 mt-0.5">Position</p>
              </div>
              <div class="text-center">
                <p class="font-display font-bold text-3xl text-forest-800">{{ $term['summary']['out_of'] }}</p>
                <p class="text-xs text-forest-600 mt-0.5">In class</p>
              </div>
            </div>
            <a href="{{ route('parent.children.report-card', [$childId, $termKey]) }}"
               class="download-btn shrink-0 text-sm px-5 py-3">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
              Download Report Card
            </a>
          </div>

          {{-- Subject results --}}
          <div class="overflow-x-auto">
            <table class="w-full border-collapse">
              <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                  <th class="px-4 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Subject</th>
                  <th class="px-3 py-3 text-center text-[11px] font-bold text-gray-500 uppercase tracking-wider hidden sm:table-cell">CA/30</th>
                  <th class="px-3 py-3 text-center text-[11px] font-bold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Exam/70</th>
                  <th class="px-3 py-3 text-center text-[11px] font-bold text-gray-500 uppercase tracking-wider">Total</th>
                  <th class="px-3 py-3 text-center text-[11px] font-bold text-gray-500 uppercase tracking-wider">Score</th>
                  <th class="px-3 py-3 text-center text-[11px] font-bold text-gray-500 uppercase tracking-wider">Grade</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                @foreach($term['subjects'] as $r)
                  <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-4 py-3 text-sm font-semibold text-gray-900 whitespace-nowrap">{{ $r['subject'] }}</td>
                    <td class="px-3 py-3 text-sm text-gray-500 text-center hidden sm:table-cell">{{ $r['ca'] }}</td>
                    <td class="px-3 py-3 text-sm text-gray-500 text-center hidden sm:table-cell">{{ $r['exam'] }}</td>
                    <td class="px-3 py-3 text-sm font-bold text-gray-900 text-center">{{ $r['total'] }}</td>
                    <td class="px-3 py-3">
                      <div class="flex items-center justify-center gap-2">
                        <div class="score-bar w-12 md:w-16">
                          <div class="score-bar-fill" data-score="{{ $r['total'] }}"></div>
                        </div>
                      </div>
                    </td>
                    <td class="px-3 py-3 text-center">
                      <span class="grade grade-{{ $r['grade'] }}">{{ $r['grade'] }}</span>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <p class="text-xs text-gray-400 mt-3 px-1">A1(75–100) · B2(70–74) · B3(65–69) · C4(60–64) · C5(55–59) · C6(50–54) · D7(45–49) · E8(40–44) · F9(0–39)</p>

        </div>
      @endif

    </div>
  @endforeach

</div>

@endsection
