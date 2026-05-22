<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Lid profiel pagina - Bekijk lid gegevens">
    <title>Lidpagina | Administratie Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/sidebar.css', 'resources/js/app.js'])
</head>
<body>
    @include('layouts.sidebar')

    <div class="main-content">
        @include('layouts.header')

        <!-- Lid Profile Section -->
        <section class="px-8 py-6">
            <div class="bg-white rounded-2xl shadow-[0_1px_3px_rgba(0,0,0,0.04),0_4px_12px_rgba(0,0,0,0.03)] border border-slate-200/60 p-8 animate-[fadeSlideUp_0.5s_ease-out]">
                <div class="flex flex-col lg:flex-row items-start lg:items-center gap-8">

                    <!-- Left: Profile Info -->
                    <div class="flex items-start gap-6 flex-1">

                        <!-- Profile Photo -->
                        <div class="relative flex-shrink-0">
                            <div class="w-[120px] h-[120px] rounded-2xl overflow-hidden border-2 border-slate-200 shadow-sm">
                                <img
                                    src="https://ui-avatars.com/api/?name=R+B&size=120&background=e0e7ff&color=1e3a8a&bold=true&font-size=0.4"
                                    alt="Profiel foto"
                                    class="w-full h-full object-cover"
                                    id="profilePhoto"
                                >
                            </div>
                            <!-- Camera Icon Overlay -->
                            <button class="absolute -bottom-1.5 -right-1.5 w-8 h-8 bg-slate-800 hover:bg-slate-700 rounded-full flex items-center justify-center shadow-lg transition-colors cursor-pointer" title="Foto wijzigen">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M23 19C23 19.5304 22.7893 20.0391 22.4142 20.4142C22.0391 20.7893 21.5304 21 21 21H3C2.46957 21 1.96086 20.7893 1.58579 20.4142C1.21071 20.0391 1 19.5304 1 19V8C1 7.46957 1.21071 6.96086 1.58579 6.58579C1.96086 6.21071 2.46957 6 3 6H7L9 3H15L17 6H21C21.5304 6 22.0391 6.21071 22.4142 6.58579C22.7893 6.96086 23 7.46957 23 8V19Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 17C14.2091 17 16 15.2091 16 13C16 10.7909 14.2091 9 12 9C9.79086 9 8 10.7909 8 13C8 15.2091 9.79086 17 12 17Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Member Details -->
                        <div class="flex flex-col gap-3 pt-1">
                            <!-- Name + LID Badge -->
                            <div class="flex items-center gap-3">
                                <h1 class="text-2xl font-bold text-slate-900 tracking-tight m-0">Regeffio Baarn</h1>
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-gradient-to-r from-cyan-500 to-teal-500 text-white text-xs font-bold tracking-wide shadow-sm">
                                    LID #9
                                </span>
                            </div>

                            <!-- Member Since & Birth Date -->
                            <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-slate-500">
                                <!-- Member since -->
                                <div class="flex items-center gap-1.5">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" class="text-slate-400" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/>
                                        <path d="M16 2V6M8 2V6M3 10H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                    <span>Member since <strong class="text-slate-700 font-semibold">23/02/2009</strong></span>
                                </div>
                                <!-- Birth date -->
                                <div class="flex items-center gap-1.5">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" class="text-slate-400" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                    <span><strong class="text-slate-700 font-semibold">23 juli 2005</strong> (20 jaar)</span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center gap-3 mt-2">
                                <button class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold rounded-xl transition-all duration-200 shadow-sm hover:shadow-md cursor-pointer">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="3" y="11" width="18" height="11" rx="2" stroke="currentColor" stroke-width="2"/>
                                        <path d="M7 11V7C7 4.23858 9.23858 2 12 2C14.7614 2 17 4.23858 17 7V11" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                    Password reset
                                </button>
                                <button class="inline-flex items-center gap-2 px-5 py-2.5 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-xl border-[1.5px] border-slate-300 hover:border-slate-400 transition-all duration-200 cursor-pointer">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M11 4H4C3.46957 4 2.96086 4.21071 2.58579 4.58579C2.21071 4.96086 2 5.46957 2 6V20C2 20.5304 2.21071 21.0391 2.58579 21.4142C2.96086 21.7893 3.46957 22 4 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M18.5 2.50001C18.8978 2.10219 19.4374 1.87869 20 1.87869C20.5626 1.87869 21.1022 2.10219 21.5 2.50001C21.8978 2.89784 22.1213 3.43739 22.1213 4.00001C22.1213 4.56262 21.8978 5.10219 21.5 5.50001L12 15L8 16L9 12L18.5 2.50001Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    Edit Profile
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="hidden lg:block w-px h-32 bg-slate-200 self-center"></div>

                    <!-- Right: Outstanding Balance Card -->
                    <div class="w-full lg:w-auto lg:min-w-[320px]">
                        <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
                            <!-- Subtle glow effect -->
                            <div class="absolute top-0 right-0 w-32 h-32 bg-cyan-500/10 rounded-full blur-2xl -translate-y-1/2 translate-x-1/2"></div>

                            <p class="text-xs font-bold tracking-[0.15em] text-slate-400 uppercase mb-3">Outstanding Balance</p>
                            <p class="text-4xl font-extrabold tracking-tight mb-5">
                                <span class="text-lg font-bold text-slate-300 mr-1">SRD</span>145.50
                            </p>

                            <div class="space-y-2.5 pt-3 border-t border-slate-700/60">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-slate-400">Last Payment</span>
                                    <span class="font-semibold text-slate-200">15 May 2023</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-slate-400">Upcoming Cost</span>
                                    <span class="font-semibold text-emerald-400">SRD 45.00</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>



        <!--Lid gegevens-->
        <div class="px-8 py-6">
            <div class="flex flex-col lg:flex-row gap-6 w-full items-start">

                <!--Contact Details Card-->
                <div class="bg-white rounded-2xl shadow-[0_1px_3px_rgba(0,0,0,0.04),0_4px_12px_rgba(0,0,0,0.03)] border border-slate-200/60 w-full lg:w-[320px] flex-shrink-0">
                    <div class="flex items-center gap-3 p-6 pb-4">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" class="text-slate-700" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M14 2V8H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <h2 class="text-base font-bold text-slate-900">Contact Details</h2>
                    </div>

                    <div class="px-6 pb-6 space-y-5">
                        <!-- Email -->
                        <div>
                            <p class="text-[11px] font-semibold tracking-[0.08em] uppercase text-slate-400 mb-1">Email Address</p>
                            <p class="text-sm font-medium text-slate-800">Regeffio@gmail.com</p>
                        </div>

                        <!-- Phone -->
                        <div>
                            <p class="text-[11px] font-semibold tracking-[0.08em] uppercase text-slate-400 mb-1">Telefoon Number</p>
                            <p class="text-sm font-medium text-slate-800">+597 334-2134</p>
                        </div>

                        <!-- Address -->
                        <div>
                            <p class="text-[11px] font-semibold tracking-[0.08em] uppercase text-slate-400 mb-1">Address</p>
                            <p class="text-sm font-medium text-slate-800">Pronk weg</p>
                            <p class="text-sm text-slate-500">Commewijne</p>
                        </div>
                    </div>
                </div>

                <!--Payment History Card-->
                <div class="bg-white rounded-2xl shadow-[0_1px_3px_rgba(0,0,0,0.04),0_4px_12px_rgba(0,0,0,0.03)] border border-slate-200/60 flex-1 min-w-0">
                    <!-- Header -->
                    <div class="flex items-center justify-between p-6 pb-4">
                        <div class="flex items-center gap-3">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" class="text-slate-700" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                <path d="M12 6V12L16 14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <h2 class="text-base font-bold text-slate-900">Payment History</h2>
                        </div>
                        <button class="w-9 h-9 flex items-center justify-center rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors cursor-pointer" title="Filter">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="text-slate-500" xmlns="http://www.w3.org/2000/svg">
                                <path d="M22 3H2L10 12.46V19L14 21V12.46L22 3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm" id="paymentHistoryTable">
                            <thead>
                                <tr class="border-t border-b border-slate-100">
                                    <th class="text-left text-[11px] font-semibold tracking-[0.08em] uppercase text-slate-400 px-6 py-3">ID</th>
                                    <th class="text-left text-[11px] font-semibold tracking-[0.08em] uppercase text-slate-400 px-6 py-3">Date</th>
                                    <th class="text-left text-[11px] font-semibold tracking-[0.08em] uppercase text-slate-400 px-6 py-3">Contribution</th>
                                    <th class="text-left text-[11px] font-semibold tracking-[0.08em] uppercase text-slate-400 px-6 py-3">Status</th>
                                    <th class="text-left text-[11px] font-semibold tracking-[0.08em] uppercase text-slate-400 px-6 py-3">Bonnummer</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4 text-slate-600 font-medium">1</td>
                                    <td class="px-6 py-4 text-slate-600">12 Jun 2023</td>
                                    <td class="px-6 py-4 text-slate-700 font-medium">SRD 45.00</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold tracking-wide bg-emerald-50 text-emerald-600">BETAALD</span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-500">—</td>
                                </tr>
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4 text-slate-600 font-medium">2</td>
                                    <td class="px-6 py-4 text-slate-600">15 May 2023</td>
                                    <td class="px-6 py-4 text-slate-700 font-medium">SRD 45.00</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold tracking-wide bg-emerald-50 text-emerald-600">BETAALD</span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-500">—</td>
                                </tr>
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4 text-slate-600 font-medium">3</td>
                                    <td class="px-6 py-4 text-slate-600">10 Apr 2023</td>
                                    <td class="px-6 py-4 text-slate-700 font-medium">SRD 45.00</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold tracking-wide bg-red-50 text-red-600">NIET BETAALD</span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-500">—</td>
                                </tr>
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4 text-slate-600 font-medium">4</td>
                                    <td class="px-6 py-4 text-slate-600">12 Mar 2023</td>
                                    <td class="px-6 py-4 text-slate-700 font-medium">SRD 45.00</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold tracking-wide bg-emerald-50 text-emerald-600">BETAALD</span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-500">—</td>
                                </tr>
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4 text-slate-600 font-medium">5</td>
                                    <td class="px-6 py-4 text-slate-600">08 Feb 2023</td>
                                    <td class="px-6 py-4 text-slate-700 font-medium">SRD 45.00</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold tracking-wide bg-emerald-50 text-emerald-600">BETAALD</span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-500">—</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100">
                        <p class="text-sm text-slate-400">Showing 1 to 5 of 24 entries</p>
                        <div class="flex items-center gap-1.5">
                            <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-900 text-white text-sm font-semibold shadow-sm">1</button>
                            <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 text-sm font-medium hover:bg-slate-50 transition-colors cursor-pointer">2</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>


    </div>
</body>
</html>
