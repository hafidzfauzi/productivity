<!DOCTYPE html>
<html lang="en" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>FocusHub — Productivity Dashboard</title>
    <meta name="description" content="Your personal productivity dashboard with focus timer, task management, music, and gamification.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body x-data="dashboard()" :class="{ 'dark': darkMode }" class="transition-theme">

    {{-- Decorative Background Mesh --}}
    <div class="bg-mesh"></div>

    {{-- ═══════════════ APP HEADER ═══════════════ --}}
    <header class="app-header transition-theme">
        <div class="flex items-center gap-3">
            <span class="app-logo">⚡ FocusHub</span>
            <span class="text-xs font-medium px-2 py-0.5 rounded-full"
                  style="background: var(--accent-tertiary); color: var(--accent-primary);">
                v1.0
            </span>
        </div>

        <div class="flex items-center gap-3">
            {{-- Greeting --}}
            <span class="text-sm font-medium hidden sm:block" style="color: var(--text-secondary);"
                  x-text="greeting"></span>

            {{-- Dark Mode Toggle --}}
            <button @click="toggleDarkMode()"
                    class="theme-toggle transition-theme"
                    id="dark-mode-toggle"
                    :aria-label="darkMode ? 'Switch to light mode' : 'Switch to dark mode'">
                <div class="theme-toggle-thumb">
                    <span x-text="darkMode ? '🌙' : '☀️'"></span>
                </div>
            </button>
        </div>
    </header>

    {{-- ═══════════════ DASHBOARD GRID ═══════════════ --}}
    <main class="dashboard-grid">

        {{-- ── ROW 1, COL 1: App Name / Quick Info ── --}}
        <div class="card animate-fade-in-up" id="widget-app-info">
            <div class="card-inner">
                <div class="card-header">
                    <div class="card-title">
                        <svg class="card-title-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        DASHBOARD
                    </div>
                </div>
                <div class="card-divider"></div>

                <div class="flex flex-col gap-3 flex-1 justify-center">
                    <h1 class="text-2xl font-bold" style="color: var(--text-primary);">
                        <span class="block text-sm font-medium mb-1" style="color: var(--text-tertiary);">
                            <span x-text="currentDate"></span> · <span x-text="currentTime" class="font-mono opacity-80"></span>
                        </span>
                        <span x-text="greetingEmoji + ' ' + greetingShort"></span>
                    </h1>
                    <p class="text-xs" style="color: var(--text-tertiary);">
                        Ready to be productive today?
                    </p>
                    <div class="flex gap-2 mt-auto">
                        <div class="text-center flex-1 p-2 rounded-lg" style="background: var(--accent-tertiary);">
                            <div class="text-lg font-bold" style="color: var(--accent-primary);" x-text="todaySessions">0</div>
                            <div class="text-xs" style="color: var(--text-tertiary);">Sessions</div>
                        </div>
                        <div class="text-center flex-1 p-2 rounded-lg" style="background: var(--accent-tertiary);">
                            <div class="text-lg font-bold" style="color: var(--accent-primary);" x-text="todayTasks">0</div>
                            <div class="text-xs" style="color: var(--text-tertiary);">Tasks</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── ROW 1, COL 2: Weather & Quotes ── --}}
        <div class="card animate-fade-in-up" id="widget-weather-quotes">
            <div class="card-inner">
                <div class="card-header">
                    <div class="card-title">
                        <svg class="card-title-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                        </svg>
                        WEATHER & QUOTES
                    </div>
                    <button class="btn-icon" @click="fetchQuote()" title="New quote">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </button>
                </div>
                <div class="card-divider"></div>

                {{-- Weather --}}
                <div class="flex items-center gap-3 mb-3">
                    <div class="text-3xl" x-text="weather.icon">🌤️</div>
                    <div>
                        <div class="weather-temp" x-text="weather.temp + '°'">--°</div>
                        <div class="weather-condition" x-text="weather.condition">Loading...</div>
                    </div>
                    <div class="ml-auto text-right">
                        <div class="text-xs font-medium" style="color: var(--text-tertiary);" x-text="weather.city">—</div>
                        <div class="text-xs" style="color: var(--text-tertiary);" x-text="'H:' + weather.humidity + '%'">H:--%</div>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="card-divider"></div>

                {{-- Quote --}}
                <div class="flex-1 flex flex-col justify-center">
                    <p class="quote-text" x-text="'“' + quote.text + '”'">Loading...</p>
                    <p class="quote-author" x-text="'— ' + quote.author">—</p>
                </div>
            </div>
        </div>

        {{-- ── ROW 1, COL 3: Music Player ── --}}
        <div class="card animate-fade-in-up" id="widget-music" style="background-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0.4) 55%, rgba(10, 10, 12, 0.95) 100%), url('/images/lofi-bg.png'); background-size: cover; background-position: center; min-height: 260px;">
            <div class="card-inner flex flex-col justify-between" style="background: transparent; backdrop-filter: none; -webkit-backdrop-filter: none;">
                <div class="card-header">
                    <div class="card-title text-white" style="color: #ffffff; text-shadow: 0 2px 4px rgba(0,0,0,0.6);">
                        <svg class="card-title-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color: #ffffff; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.5));">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                        </svg>
                        LOFI CAFE
                    </div>
                </div>

                {{-- Lo-Fi Cafe Embed --}}
                <div class="w-full flex items-center justify-center rounded-xl overflow-hidden mt-auto" style="background: rgba(0, 0, 0, 0.45); backdrop-filter: blur(10px); padding: 6px; border: 1px solid rgba(255, 255, 255, 0.1);">
                    <iframe src="https://loficafe.net/embed/chilling?utm_source=embed&utm_medium=iframe&utm_campaign=station_embed"
                            width="100%"
                            height="80"
                            frameborder="0"
                            allow="autoplay"
                            style="border-radius: 8px; border: none;">
                    </iframe>
                </div>
            </div>
        </div>

        {{-- ── ROW 1, COL 4: Focus Timer / Power Nap ── --}}
        <div class="card animate-fade-in-up" id="widget-timer">
            <div class="card-inner">
                <div class="card-header">
                    <div class="card-title">
                        <svg class="card-title-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        FOCUS TIMER
                    </div>
                </div>
                <div class="card-divider"></div>

                {{-- Mode Tabs --}}
                <div class="flex gap-1 mb-3 justify-center">
                    <template x-for="m in timerModes" :key="m.key">
                        <button class="mode-btn"
                                :class="{ 'active': timerMode === m.key }"
                                @click="setTimerMode(m.key)"
                                x-text="m.label"></button>
                    </template>
                </div>

                {{-- Timer Circle --}}
                <div class="flex justify-center mb-3">
                    <div class="timer-circle">
                        <svg viewBox="0 0 100 100">
                            <circle class="timer-circle-bg" cx="50" cy="50" r="44"></circle>
                            <circle class="timer-circle-progress"
                                    cx="50" cy="50" r="44"
                                    :stroke-dasharray="2 * Math.PI * 44"
                                    :stroke-dashoffset="2 * Math.PI * 44 * (1 - timerProgress)">
                            </circle>
                        </svg>
                        <div class="timer-display">
                            <div class="timer-time" x-text="formatTime(timerTimeLeft)">25:00</div>
                            <div class="timer-label" x-text="timerModeLabel">focus</div>
                        </div>
                    </div>
                </div>

                {{-- Controls --}}
                <div class="flex justify-center gap-2">
                    <button class="btn-primary text-xs px-4"
                            @click="toggleTimer()"
                            x-text="timerRunning ? '⏸ Pause' : (timerTimeLeft < timerDuration ? '▶ Resume' : '▶ Start')">
                        ▶ Start
                    </button>
                    <button class="btn-ghost text-xs"
                            @click="resetTimer()"
                            x-show="timerTimeLeft < timerDuration || timerRunning">
                        ↻ Reset
                    </button>
                </div>
            </div>
        </div>

        {{-- ══════════════ ROW 2 ══════════════ --}}

        {{-- ── ROW 2, COL 1: Profile & Stats ── --}}
        <div class="card animate-fade-in-up" id="widget-profile">
            <div class="card-inner">
                <div class="card-header">
                    <div class="card-title">
                        <svg class="card-title-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        PROFILE & STATS
                    </div>
                </div>
                <div class="card-divider"></div>

                <div class="flex flex-col items-center gap-2 mb-3">
                    {{-- Avatar --}}
                    <div class="w-14 h-14 rounded-full flex items-center justify-center text-2xl"
                         style="background: var(--gradient-accent);">
                        🧑‍💻
                    </div>
                    <div class="text-center">
                        <div class="font-bold text-sm" style="color: var(--text-primary);">User</div>
                        <div class="text-xs" style="color: var(--text-tertiary);">Productivity Explorer</div>
                    </div>
                </div>

                {{-- Level --}}
                <div class="mb-2">
                    <div class="flex justify-between text-xs mb-1">
                        <span style="color: var(--text-tertiary);">Level <span class="font-bold" style="color: var(--accent-primary);" x-text="gamification.level">1</span></span>
                        <span style="color: var(--text-tertiary);" x-text="gamification.xpInLevel + '/' + gamification.xpPerLevel + ' XP'">0/600 XP</span>
                    </div>
                    <div class="xp-bar-track">
                        <div class="xp-bar-fill" :style="'width:' + gamification.xpPercent + '%'"></div>
                    </div>
                </div>

                {{-- Streak --}}
                <div class="flex justify-center mt-1">
                    <div class="streak-badge" x-show="gamification.streak > 0">
                        🔥 <span x-text="gamification.streak + ' day streak'"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── ROW 2, COL 2: Calendar ── --}}
        <div class="card animate-fade-in-up" id="widget-calendar">
            <div class="card-inner">
                <div class="card-header">
                    <div class="card-title">
                        <svg class="card-title-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        CALENDAR
                    </div>
                    <div class="flex gap-1">
                        <button class="btn-icon" @click="calendarPrevMonth()">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button class="btn-icon" @click="calendarNextMonth()">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>
                <div class="card-divider"></div>

                <div class="text-center text-xs font-semibold mb-2" style="color: var(--text-secondary);" x-text="calendarMonthLabel"></div>

                {{-- Day names --}}
                <div class="grid grid-cols-7 gap-0.5 mb-1">
                    <template x-for="d in ['Su','Mo','Tu','We','Th','Fr','Sa']">
                        <div class="text-center text-xs font-medium py-1" style="color: var(--text-tertiary);" x-text="d"></div>
                    </template>
                </div>

                {{-- Day numbers --}}
                <div class="grid grid-cols-7 gap-0.5">
                    <template x-for="day in calendarDays" :key="day.key">
                        <div class="calendar-day relative"
                             :class="{ 'today': day.isToday, 'opacity-30': !day.currentMonth }"
                             x-text="day.date">
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- ── ROW 2, COL 3: To Do List ── --}}
        <div class="card animate-fade-in-up" id="widget-tasks">
            <div class="card-inner">
                <div class="card-header">
                    <div class="card-title">
                        <svg class="card-title-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        TO DO LIST
                    </div>
                    <button class="btn-icon" @click="showAddTask = !showAddTask" title="Add task">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                    </button>
                </div>
                <div class="card-divider"></div>

                {{-- Add Task --}}
                <div x-show="showAddTask" x-transition class="mb-3 flex gap-1">
                    <input type="text" class="input-field text-xs flex-1"
                           placeholder="What needs to be done?"
                           x-model="newTaskText"
                           @keydown.enter="addTask()"
                           x-ref="taskInput">
                    <button class="btn-primary text-xs" @click="addTask()">Add</button>
                </div>

                {{-- Task Summary --}}
                <div class="flex gap-2 mb-2 text-xs" style="color: var(--text-tertiary);">
                    <span x-text="tasks.filter(t => t.is_completed).length + ' done'"></span>
                    <span>·</span>
                    <span x-text="tasks.filter(t => !t.is_completed).length + ' pending'"></span>
                </div>

                {{-- Tasks --}}
                <div class="flex-1 overflow-y-auto space-y-0.5" style="max-height: 180px;">
                    <template x-for="task in tasks" :key="task.id">
                        <div class="task-item animate-slide-in">
                            <button class="task-checkbox"
                                    :class="{ 'checked': task.is_completed }"
                                    @click="toggleTask(task)">
                                <svg x-show="task.is_completed" width="10" height="10" fill="white" viewBox="0 0 24 24">
                                    <path d="M20.285 2l-11.285 11.567-5.286-5.011-3.714 3.716 9 8.728 15-15.285z"/>
                                </svg>
                            </button>
                            <span class="task-text" :class="{ 'completed': task.is_completed }" x-text="task.title"></span>
                            <button class="task-delete btn-icon" @click="deleteTask(task)" title="Delete">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </template>

                    <div x-show="tasks.length === 0" class="text-center py-4 text-xs" style="color: var(--text-tertiary);">
                        No tasks yet. Add one! ✨
                    </div>
                </div>
            </div>
        </div>

        {{-- ── ROW 2, COL 4: Prayer Reminder & Lunch Break ── --}}
        <div class="card animate-fade-in-up" id="widget-prayer">
            <div class="card-inner">
                <div class="card-header">
                    <div class="card-title">
                        <svg class="card-title-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        PRAYER & BREAK
                    </div>
                </div>
                <div class="card-divider"></div>

                {{-- Next Prayer --}}
                <div class="p-2 rounded-xl mb-3 text-center" style="background: var(--accent-tertiary);">
                    <div class="text-xs font-medium mb-0.5" style="color: var(--text-tertiary);">Next Prayer</div>
                    <div class="text-lg font-bold" style="color: var(--accent-primary);" x-text="prayer.nextName || '—'">—</div>
                    <div class="text-xs font-mono font-semibold" style="color: var(--text-secondary);" x-text="prayer.nextTime || '--:--'">--:--</div>
                </div>

                {{-- Prayer Times List --}}
                <div class="space-y-0.5 flex-1 overflow-y-auto" style="max-height: 130px;">
                    <template x-for="p in prayer.times" :key="p.name">
                        <div class="prayer-item" :class="{ 'prayer-next': p.isNext }">
                            <span class="prayer-name" x-text="p.name"></span>
                            <span class="prayer-time" x-text="p.time"></span>
                        </div>
                    </template>
                </div>

                {{-- Lunch Break --}}
                <div class="card-divider mt-2"></div>
                <div class="flex items-center justify-between mt-1">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">🍱</span>
                        <span class="text-xs font-medium" style="color: var(--text-secondary);">Lunch Break</span>
                    </div>
                    <span class="text-xs font-mono font-semibold" style="color: var(--accent-primary);">12:00</span>
                </div>
            </div>
        </div>

        {{-- ══════════════ ROW 3: Full Width Gamified Tracker ══════════════ --}}
        <div class="card animate-fade-in-up widget-full" id="widget-gamification">
            <div class="card-inner">
                <div class="card-header">
                    <div class="card-title">
                        <svg class="card-title-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        GAMIFIED PRODUCTIVITY TRACKER
                    </div>
                    <div class="streak-badge" x-show="gamification.streak > 0">
                        🔥 <span x-text="gamification.streak + ' day streak'"></span>
                    </div>
                </div>
                <div class="card-divider"></div>

                <div class="flex flex-col sm:flex-row gap-4">
                    {{-- Left: Stats --}}
                    <div class="flex-1">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div class="text-center p-3 rounded-xl" style="background: var(--accent-tertiary);">
                                <div class="text-2xl font-bold" style="color: var(--accent-primary);" x-text="gamification.totalSessions">0</div>
                                <div class="text-xs mt-1" style="color: var(--text-tertiary);">Total Sessions</div>
                            </div>
                            <div class="text-center p-3 rounded-xl" style="background: var(--accent-tertiary);">
                                <div class="text-2xl font-bold" style="color: var(--accent-primary);" x-text="gamification.totalXp">0</div>
                                <div class="text-xs mt-1" style="color: var(--text-tertiary);">Total XP</div>
                            </div>
                            <div class="text-center p-3 rounded-xl" style="background: var(--accent-tertiary);">
                                <div class="text-2xl font-bold" style="color: var(--accent-primary);" x-text="gamification.level">1</div>
                                <div class="text-xs mt-1" style="color: var(--text-tertiary);">Level</div>
                            </div>
                            <div class="text-center p-3 rounded-xl" style="background: var(--accent-tertiary);">
                                <div class="text-2xl font-bold" style="color: var(--accent-primary);" x-text="gamification.longestStreak">0</div>
                                <div class="text-xs mt-1" style="color: var(--text-tertiary);">Best Streak</div>
                            </div>
                        </div>
                    </div>

                    {{-- Right: XP Progress --}}
                    <div class="flex-1 flex flex-col justify-center">
                        <div class="flex justify-between text-xs mb-2">
                            <span class="font-semibold" style="color: var(--text-secondary);">Level Progress</span>
                            <span style="color: var(--accent-primary);" x-text="gamification.xpInLevel + ' / ' + gamification.xpPerLevel + ' XP'"></span>
                        </div>
                        <div class="xp-bar-track" style="height: 12px;">
                            <div class="xp-bar-fill" :style="'width:' + gamification.xpPercent + '%'"></div>
                        </div>

                        {{-- Stage Markers --}}
                        <div class="flex justify-between mt-3">
                            <template x-for="stage in [1,2,3,4,5]" :key="stage">
                                <div class="flex flex-col items-center">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm border-2 transition-all"
                                         :class="stage <= gamification.currentStage
                                            ? 'border-transparent'
                                            : ''"
                                         :style="stage <= gamification.currentStage
                                            ? 'background: var(--gradient-accent); color: white; box-shadow: 0 2px 8px var(--accent-glow);'
                                            : 'background: var(--accent-tertiary); color: var(--text-tertiary); border-color: var(--border-color);'"
                                         x-text="['🌱','🌿','🌳','🌸','🌺'][stage - 1]">
                                    </div>
                                    <span class="text-xs mt-1" style="color: var(--text-tertiary);" x-text="'Stg ' + stage"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>

</body>
</html>
