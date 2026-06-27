@vite('resources/js/UI/Notificatie.js')

<div class="relative"
     x-data="{
        open: false,
        notificaties: [],
        ongelezen: 0,

        // Icon en kleur per notificatie type
        typeStyles: {
            'Betaling_ingediend':    { icon: 'fa-file-invoice-dollar', color: 'blue' },
            'betaling_goedgekeurd':  { icon: 'fa-circle-check',         color: 'emerald' },
            'betaling_afgewezen':    { icon: 'fa-circle-xmark',         color: 'rose' },
            'betaling_herinnering':  { icon: 'fa-clock',                color: 'amber' },
            'default':               { icon: 'fa-bell',                 color: 'slate' }
        },
        style(type) {
            return this.typeStyles[type] || this.typeStyles.default;
        },

        fetchNotifications() {
            fetch('{{ route('notificaties.index') }}')
                .then(res => res.json())
                .then(data => {
                    this.notificaties = data.notificaties;
                    this.ongelezen = data.ongelezen;
                });
        },
        markAsRead() {
            if (this.ongelezen === 0) return;
            fetch('{{ route('notificaties.lezen') }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(() => {
                this.ongelezen = 0;
                this.notificaties.forEach(n => n.gelezen = true);
            });
        },
        relativeTime(date) {
            if (!date) return '';
            const sec = Math.floor((new Date() - new Date(date)) / 1000);
            if (sec < 60) return 'Zojuist';
            if (sec < 3600) return Math.floor(sec / 60) + ' min geleden';
            if (sec < 86400) return Math.floor(sec / 3600) + ' uur geleden';
            const days = Math.floor(sec / 86400);
            if (days < 7) return days + ' dag' + (days > 1 ? 'en' : '') + ' geleden';
            return new Date(date).toLocaleDateString('nl-NL', { day: 'numeric', month: 'short' });
        }
     }"
     x-init="fetchNotifications(); setInterval(fetchNotifications, 30000)"
     @click.outside="open = false">

    <!-- Bel knop -->
    <button @click="open = !open"
            class="relative p-2.5 rounded-xl text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
        <i class="fa-regular fa-bell text-lg"
           :class="ongelezen > 0 ? 'animate-[swing_1.5s_ease-in-out_infinite]' : ''"></i>

        <span x-show="ongelezen > 0"
              x-text="ongelezen > 9 ? '9+' : ongelezen"
              class="absolute -top-0.5 -right-0.5 bg-rose-500 text-white font-bold text-[10px] rounded-full min-w-[18px] h-[18px] px-1 flex items-center justify-center border-2 border-white shadow-md"></span>
    </button>

    <!-- Dropdown -->
    <div x-show="open"
         x-cloak
         x-transition.origin.top.right.duration.200ms
         class="absolute right-0 mt-3 w-[400px] max-w-[calc(100vw-2rem)] bg-white border border-gray-200 rounded-2xl shadow-xl z-50 overflow-hidden">

        <!-- Header -->
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center">
                    <i class="fa-solid fa-bell text-sm"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 text-sm">Notificaties</h3>
                    <p class="text-[11px] text-gray-500"
                       x-text="ongelezen > 0 ? ongelezen + ' nieuwe meldingen' : 'Alles bekeken'"></p>
                </div>
            </div>

            <button x-show="ongelezen > 0"
                    @click="markAsRead()"
                    class="text-[11px] font-semibold text-indigo-600 hover:bg-indigo-50 px-2.5 py-1.5 rounded-lg transition">
                <i class="fa-solid fa-check text-[10px] mr-1"></i>Markeer gelezen
            </button>
        </div>

        <!-- Lijst -->
        <div class="max-h-[420px] overflow-y-auto divide-y divide-gray-100 scrollbar-thin">

            <!-- Lege staat -->
            <template x-if="notificaties.length === 0">
                <div class="px-6 py-12 text-center">
                    <div class="w-16 h-16 mx-auto rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center mb-4">
                        <i class="fa-solid fa-check-circle text-2xl"></i>
                    </div>
                    <p class="text-sm font-bold text-gray-800">Helemaal bij!</p>
                    <p class="text-xs text-gray-500 mt-1">Er zijn geen nieuwe meldingen.</p>
                </div>
            </template>

            <!-- Notificatie items -->
            <template x-for="n in notificaties" :key="n.notificatie_id">
                <div class="px-4 py-3.5 hover:bg-slate-50 transition flex gap-3 items-start relative cursor-pointer"
                     :class="!n.gelezen ? 'bg-indigo-50/40' : ''">

                    <!-- Icoon -->
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow-sm ring-1"
                         :class="`bg-${style(n.Notif_type).color}-50 text-${style(n.Notif_type).color}-600 ring-${style(n.Notif_type).color}-200/50`">
                        <i class="fa-solid text-sm" :class="style(n.Notif_type).icon"></i>
                    </div>

                    <!-- Tekst -->
                    <div class="flex-1 min-w-0 pr-4">
                        <p class="text-xs leading-relaxed text-gray-800"
                           :class="!n.gelezen ? 'font-semibold' : 'font-normal text-gray-600'"
                           x-text="n.titel"></p>
                        <div class="flex items-center gap-1.5 mt-1.5">
                            <i class="fa-regular fa-clock text-[9px] text-gray-400"></i>
                            <span class="text-[10px] text-gray-400 font-medium" x-text="relativeTime(n.gestuurd_op)"></span>
                        </div>
                    </div>

                    <!-- Ongelezen indicator -->
                    <span x-show="!n.gelezen"
                          class="absolute right-4 top-1/2 -translate-y-1/2 w-2 h-2 rounded-full bg-indigo-500 ring-4 ring-indigo-100"></span>
                </div>
            </template>
        </div>

        <!-- Footer -->
        <div x-show="notificaties.length > 0"
             class="px-5 py-3 border-t border-gray-100 bg-slate-50/50 text-center">
            <span class="text-[11px] text-gray-500 font-medium">
                Toont laatste <span class="font-bold text-gray-700" x-text="notificaties.length"></span> meldingen
            </span>
        </div>
    </div>
</div>


 

<style>
    @keyframes swing {
        0%, 100% { transform: rotate(0); }
        20% { transform: rotate(12deg); }
        40% { transform: rotate(-10deg); }
        60% { transform: rotate(6deg); }
        80% { transform: rotate(-4deg); }
    }
    .scrollbar-thin::-webkit-scrollbar { width: 6px; }
    .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
    .scrollbar-thin::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 9999px; }
    .scrollbar-thin::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>
