  @vite('resources/js/UI/Notificatie.js')
  
  <div class="relative"
     x-data="{
        open: false,
        notificaties: [],
        ongelezen: 0,
        fetchNotifications() {
            fetch('{{ route('notificaties.index') }}')
                .then(res => res.json())
                .then(data => {
                    this.notificaties = data.notificaties;
                    this.ongelezen = data.ongelezen;
                });
        },
        markAsRead() {
            if (this.ongelezen > 0) {
                fetch('{{ route('notificaties.lezen') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                })
                .then(res => res.json())
                .then(() => {
                    this.ongelezen = 0;
                    this.notificaties.forEach(n => n.gelezen = true);
                });
            }
        }
     }"
     x-init="fetchNotifications(); setInterval(() => fetchNotifications(), 30000)"
     @click.outside="open = false">

    <!-- Bell Toggle Button -->
    <button @click="open = !open; if(open) markAsRead()"
            class="relative p-2 rounded-xl text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-all duration-200 focus:outline-none flex items-center justify-center">
        <i class="fa-solid fa-bell text-lg" :class="ongelezen > 0 ? 'animate-[swing_1s_ease-in-out_infinite]' : ''"></i>

        <!-- Unread badge -->
        <span x-show="ongelezen > 0"
              x-text="ongelezen"
              class="absolute -top-0.5 -right-0.5 bg-rose-500 text-white font-bold text-[10px] rounded-full min-w-5 h-5 px-1 flex items-center justify-center border-2 border-white shadow-sm animate-pulse">
        </span>
    </button>

    <!-- Dropdown Menu -->
    <div x-show="open"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-3"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-3"
         class="absolute right-0 mt-3 w-96 max-w-[calc(100vw-2rem)] bg-white border border-gray-100 rounded-2xl shadow-[0_10px_30px_rgba(0,0,0,0.08)] z-50 overflow-hidden">

        <!-- Header -->
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center gap-2">
                <span class="font-bold text-gray-900 text-sm">Notificaties</span>
                <span x-show="ongelezen > 0"
                      x-text="ongelezen + ' nieuw'"
                      class="bg-indigo-50 text-indigo-600 text-[10px] font-bold px-2 py-0.5 rounded-full">
                </span>
            </div>

        </div>

        <!-- Notification List -->
        <div class="max-h-[360px] overflow-y-auto divide-y divide-gray-50 scrollbar-thin">
            <!-- Empty State -->
            <template x-if="notificaties.length === 0">
                <div class="p-8 text-center flex flex-col items-center justify-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center">
                        <i class="fa-solid fa-circle-check text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Je bent helemaal bij!</p>
                        <p class="text-xs text-gray-400 mt-0.5">Er zijn momenteel geen meldingen.</p>
                    </div>
                </div>
            </template>

            <!-- Notification Items -->
            <template x-for="notificatie in notificaties" :key="notificatie.notificatie_id">
                <div class="p-4 hover:bg-slate-50/50 transition-colors flex gap-3 items-start relative"
                     :class="!notificatie.gelezen ? 'bg-indigo-50/20' : ''">

                    <!-- Icon based on Notif_type -->
                    <div class="shrink-0">
                        <!-- Betaling ingediend -->
                        <template x-if="notificatie.Notif_type === 'Betaling_ingediend'">
                            <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                <i class="fa-solid fa-file-invoice-dollar text-sm"></i>
                            </div>
                        </template>

                        <!-- Betaling goedgekeurd -->
                        <template x-if="notificatie.Notif_type === 'betaling_goedgekeurd'">
                            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <i class="fa-solid fa-check-double text-sm"></i>
                            </div>
                        </template>

                        <!-- Betaling herinnering -->
                        <template x-if="notificatie.Notif_type === 'betaling_herinnering'">
                            <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                                <i class="fa-solid fa-clock text-sm"></i>
                            </div>
                        </template>

                        <!-- Default / Other types -->
                        <template x-if="notificatie.Notif_type !== 'Betaling_ingediend' && notificatie.Notif_type !== 'betaling_goedgekeurd' && notificatie.Notif_type !== 'betaling_herinnering'">
                            <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center">
                                <i class="fa-solid fa-bell text-sm"></i>
                            </div>
                        </template>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                     <p class="text-xs text-gray-700 leading-relaxed font-medium" x-text="notificatie.titel"></p>
                    </div>

                    <!-- Unread dot indicator -->
                    <template x-if="!notificatie.gelezen">
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center">
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 ring-4 ring-indigo-50"></span>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>

<style>
    /* Subtle bell swing animation */
    @keyframes swing {
        0% { transform: rotate(0); }
        15% { transform: rotate(10deg); }
        30% { transform: rotate(-10deg); }
        45% { transform: rotate(4deg); }
        60% { transform: rotate(-4deg); }
        75% { transform: rotate(2deg); }
        85% { transform: rotate(-2deg); }
        100% { transform: rotate(0); }
    }

    /* Sleek scrollbar styles */
    .scrollbar-thin::-webkit-scrollbar {
        width: 6px;
    }
    .scrollbar-thin::-webkit-scrollbar-track {
        background: transparent;
    }
    .scrollbar-thin::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 9999px;
    }
    .scrollbar-thin::-webkit-scrollbar-thumb:hover {
        background: #cbd5e1;
    }
</style>
