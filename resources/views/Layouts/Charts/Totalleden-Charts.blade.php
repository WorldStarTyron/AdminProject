<div class="bg-white rounded-2xl p-6 shadow-[0_1px_3px_rgba(0,0,0,0.04),0_4px_12px_rgba(0,0,0,0.03)] border border-slate-200/60 h-full flex flex-col animate-[fadeSlideUp_0.5s_ease-out_0.1s_both]">
    <div class="flex justify-between items-center mb-4">
        <h3 class="m-0 text-[0.9375rem] font-bold text-slate-800 tracking-tight">Leden activiteit</h3>
        <span class="text-xs font-semibold text-slate-400 bg-slate-100 py-0.5 px-2.5 rounded-md">{{ date('Y') }}</span>
    </div>
    <div class="flex-1 min-h-[180px] relative">
        <canvas id="joinChart" data-labels='@json($labels)' data-values='@json($values)'></canvas>
    </div>
</div>
