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
                        <span class="block text-sm font-medium mb-1" style="color: var(--text-tertiary);" x-text="currentDate"></span>
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
        <div class="card animate-fade-in-up" id="widget-music">
            <div class="card-inner">
                <div class="card-header">
                    <div class="card-title">
                        <svg class="card-title-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                        </svg>
                        MUSIC PLAYER
                    </div>
                    <button class="btn-icon" @click="showPlaylistPicker = !showPlaylistPicker" title="Choose playlist">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/>
                        </svg>
                    </button>
                </div>
                <div class="card-divider"></div>

                {{-- Playlist Picker --}}
                <div x-show="showPlaylistPicker" x-transition class="mb-3">
                    <template x-for="pl in playlists" :key="pl.id">
                        <div class="playlist-item"
                             :class="{ 'active': currentPlaylist?.id === pl.id }"
                             @click="selectPlaylist(pl)">
                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                            <div>
                                <div class="font-medium text-xs" x-text="pl.name"></div>
                                <div class="text-xs opacity-60" x-text="pl.artist"></div>
                            </div>
                        </div>
                    </template>

                    {{-- Custom URL --}}
                    <div class="mt-2 flex gap-1">
                        <input type="text" class="input-field text-xs flex-1"
                               placeholder="Paste Spotify URL..."
                               x-model="customSpotifyUrl"
                               @keydown.enter="loadCustomPlaylist()">
                        <button class="btn-ghost text-xs" @click="loadCustomPlaylist()">Load</button>
                    </div>
                </div>

                {{-- Spotify Embed --}}
                <div class="flex-1 rounded-xl overflow-hidden" style="min-height: 152px;">
                    <template x-if="currentPlaylist">
                        <iframe
                            :src="'https://open.spotify.com/embed/' + spotifyEmbedType + '/' + currentPlaylist.id + '?utm_source=generator&theme=0'"
                            width="100%"
                            height="152"
                            frameBorder="0"
                            allowfullscreen=""
                            allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                            loading="lazy"
                            style="border-radius: 12px;">
                        </iframe>
                    </template>
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

    {{-- ═══════════════ ALPINE.JS APP ═══════════════ --}}
    <script>
        function dashboard() {
            return {
                // ── Dark Mode ──
                darkMode: localStorage.getItem('darkMode') === 'true',

                toggleDarkMode() {
                    this.darkMode = !this.darkMode;
                    localStorage.setItem('darkMode', this.darkMode);
                },

                // ── Greeting ──
                get greeting() {
                    const h = new Date().getHours();
                    if (h < 12) return 'Good Morning ☀️';
                    if (h < 17) return 'Good Afternoon 🌤️';
                    if (h < 20) return 'Good Evening 🌅';
                    return 'Good Night 🌙';
                },

                get greetingShort() {
                    const h = new Date().getHours();
                    if (h < 12) return 'Good Morning';
                    if (h < 17) return 'Good Afternoon';
                    if (h < 20) return 'Good Evening';
                    return 'Good Night';
                },

                get greetingEmoji() {
                    const h = new Date().getHours();
                    if (h < 12) return '☀️';
                    if (h < 17) return '🌤️';
                    if (h < 20) return '🌅';
                    return '🌙';
                },

                get currentDate() {
                    return new Date().toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
                },

                // ── Timer ──
                timerMode: 'focus',
                timerTimeLeft: 25 * 60,
                timerRunning: false,
                timerEndTime: null,
                timerInterval: null,
                todaySessions: 0,

                timerModes: [
                    { key: 'focus', label: 'Focus', duration: 25 * 60 },
                    { key: 'short', label: 'Short', duration: 5 * 60 },
                    { key: 'long', label: 'Long', duration: 15 * 60 },
                    { key: 'nap', label: 'Nap', duration: 20 * 60 },
                ],

                get timerDuration() {
                    return this.timerModes.find(m => m.key === this.timerMode)?.duration || 25 * 60;
                },

                get timerProgress() {
                    const dur = this.timerDuration;
                    return (dur - this.timerTimeLeft) / dur;
                },

                get timerModeLabel() {
                    return this.timerModes.find(m => m.key === this.timerMode)?.label || 'Focus';
                },

                formatTime(seconds) {
                    const m = Math.floor(seconds / 60);
                    const s = seconds % 60;
                    return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
                },

                setTimerMode(mode) {
                    if (this.timerRunning) return;
                    this.timerMode = mode;
                    this.timerTimeLeft = this.timerDuration;
                },

                toggleTimer() {
                    if (this.timerRunning) {
                        this.pauseTimer();
                    } else {
                        this.startTimer();
                    }
                },

                startTimer() {
                    this.timerRunning = true;
                    this.timerEndTime = Date.now() + this.timerTimeLeft * 1000;

                    this.timerInterval = setInterval(() => {
                        const remaining = Math.max(0, Math.ceil((this.timerEndTime - Date.now()) / 1000));
                        this.timerTimeLeft = remaining;

                        if (remaining === 0) {
                            this.timerRunning = false;
                            clearInterval(this.timerInterval);
                            this.timerInterval = null;
                            this.onTimerComplete();
                        }
                    }, 250);
                },

                pauseTimer() {
                    this.timerRunning = false;
                    clearInterval(this.timerInterval);
                    this.timerInterval = null;
                },

                resetTimer() {
                    this.pauseTimer();
                    this.timerTimeLeft = this.timerDuration;
                },

                onTimerComplete() {
                    this.playChime();

                    if (this.timerMode === 'focus') {
                        this.todaySessions++;
                        this.recordSession();
                    }

                    if ('Notification' in window && Notification.permission === 'granted') {
                        try {
                            new Notification('FocusHub ⚡', {
                                body: this.timerMode === 'focus'
                                    ? 'Focus session complete! Great work! 🎉'
                                    : 'Break is over. Ready to focus again? 💪',
                                tag: 'focushub-timer',
                            });
                        } catch {}
                    }
                },

                playChime() {
                    try {
                        const Ctx = window.AudioContext || window.webkitAudioContext;
                        const ctx = new Ctx();
                        const tone = (freq, start, len, gain = 0.18) => {
                            const osc = ctx.createOscillator();
                            const g = ctx.createGain();
                            osc.connect(g);
                            g.connect(ctx.destination);
                            osc.frequency.value = freq;
                            osc.type = 'triangle';
                            g.gain.setValueAtTime(0, start);
                            g.gain.linearRampToValueAtTime(gain, start + 0.04);
                            g.gain.exponentialRampToValueAtTime(0.0001, start + len);
                            osc.start(start);
                            osc.stop(start + len);
                        };
                        const t = ctx.currentTime;
                        tone(659.25, t, 0.5, 0.18);
                        tone(783.99, t + 0.18, 0.6, 0.16);
                        tone(1046.5, t + 0.42, 0.8, 0.14);
                    } catch {}
                },

                async recordSession() {
                    try {
                        await fetch('/api/focus-sessions', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                mode: this.timerMode,
                                duration_minutes: this.timerDuration / 60
                            })
                        });
                        this.loadGamification();
                    } catch {}
                },

                // ── Tasks ──
                tasks: [],
                newTaskText: '',
                showAddTask: false,
                todayTasks: 0,

                async loadTasks() {
                    try {
                        const res = await fetch('/api/tasks');
                        this.tasks = await res.json();
                        this.todayTasks = this.tasks.filter(t => !t.is_completed).length;
                    } catch {}
                },

                async addTask() {
                    const text = this.newTaskText.trim();
                    if (!text) return;
                    try {
                        const res = await fetch('/api/tasks', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ title: text })
                        });
                        const task = await res.json();
                        this.tasks.unshift(task);
                        this.newTaskText = '';
                        this.todayTasks = this.tasks.filter(t => !t.is_completed).length;
                    } catch {}
                },

                async toggleTask(task) {
                    try {
                        await fetch(`/api/tasks/${task.id}`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ is_completed: !task.is_completed })
                        });
                        task.is_completed = !task.is_completed;
                        this.todayTasks = this.tasks.filter(t => !t.is_completed).length;
                    } catch {}
                },

                async deleteTask(task) {
                    try {
                        await fetch(`/api/tasks/${task.id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                        this.tasks = this.tasks.filter(t => t.id !== task.id);
                        this.todayTasks = this.tasks.filter(t => !t.is_completed).length;
                    } catch {}
                },

                // ── Music ──
                playlists: [
                    { name: 'Cloudy Day', artist: 'Lofi Girl', id: '0vvXsWCC9xrXsKd4FyS8kM' },
                    { name: 'Deep Focus', artist: 'Spotify', id: '37i9dQZF1DWZeKCadgRdKQ' },
                    { name: 'Peaceful Piano', artist: 'Spotify', id: '37i9dQZF1DX4sWSpwq3LiO' },
                    { name: 'Jazz Vibes', artist: 'Spotify', id: '37i9dQZF1DX0SM0LYsmbMT' },
                ],
                currentPlaylist: null,
                showPlaylistPicker: false,
                customSpotifyUrl: '',
                spotifyEmbedType: 'playlist',

                selectPlaylist(pl) {
                    this.spotifyEmbedType = 'playlist';
                    this.currentPlaylist = { ...pl };
                    this.showPlaylistPicker = false;
                },

                loadCustomPlaylist() {
                    const match = this.customSpotifyUrl.match(/spotify\.com\/(playlist|album|track)\/([a-zA-Z0-9]+)/);
                    if (!match) return;
                    this.spotifyEmbedType = match[1];
                    this.currentPlaylist = { name: 'Custom', artist: 'You', id: match[2] };
                    this.customSpotifyUrl = '';
                    this.showPlaylistPicker = false;
                },

                // ── Weather & Location ──
                latitude: null,
                longitude: null,
                weather: { temp: '--', condition: 'Loading...', icon: '🌤️', city: '—', humidity: '--' },

                initLocation() {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            (position) => {
                                this.latitude = position.coords.latitude;
                                this.longitude = position.coords.longitude;
                                this.fetchWeather();
                                this.fetchPrayerTimes();
                            },
                            (error) => {
                                console.warn("Geolocation failed or denied. Using default location (Cilacap Utara).", error);
                                this.fetchWeather();
                                this.fetchPrayerTimes();
                            },
                            { enableHighAccuracy: true, timeout: 5000, maximumAge: 600000 }
                        );
                    } else {
                        this.fetchWeather();
                        this.fetchPrayerTimes();
                    }
                },

                async fetchWeather() {
                    try {
                        let url = '/api/weather';
                        if (this.latitude !== null && this.longitude !== null) {
                            url += `?latitude=${this.latitude}&longitude=${this.longitude}`;
                        }
                        const res = await fetch(url);
                        if (res.ok) {
                            this.weather = await res.json();
                        }
                    } catch {}
                },

                // ── Quotes ──
                quote: { text: 'Small steps every day lead to big changes.', author: 'Unknown' },

                async fetchQuote() {
                    try {
                        const res = await fetch('/api/quote');
                        if (res.ok) {
                            this.quote = await res.json();
                        }
                    } catch {}
                },

                // ── Gamification ──
                gamification: {
                    totalSessions: 0,
                    totalXp: 0,
                    level: 1,
                    xpInLevel: 0,
                    xpPerLevel: 600,
                    xpPercent: 0,
                    streak: 0,
                    longestStreak: 0,
                    currentStage: 0,
                },

                async loadGamification() {
                    try {
                        const res = await fetch('/api/gamification');
                        if (res.ok) {
                            this.gamification = await res.json();
                        }
                    } catch {}
                },

                // ── Calendar ──
                calendarDate: new Date(),

                get calendarMonthLabel() {
                    return this.calendarDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
                },

                get calendarDays() {
                    const year = this.calendarDate.getFullYear();
                    const month = this.calendarDate.getMonth();
                    const firstDay = new Date(year, month, 1).getDay();
                    const daysInMonth = new Date(year, month + 1, 0).getDate();
                    const daysInPrevMonth = new Date(year, month, 0).getDate();
                    const today = new Date();

                    const days = [];

                    // Previous month days
                    for (let i = firstDay - 1; i >= 0; i--) {
                        days.push({
                            key: `prev-${i}`,
                            date: daysInPrevMonth - i,
                            currentMonth: false,
                            isToday: false,
                        });
                    }

                    // Current month days
                    for (let d = 1; d <= daysInMonth; d++) {
                        days.push({
                            key: `cur-${d}`,
                            date: d,
                            currentMonth: true,
                            isToday: d === today.getDate() && month === today.getMonth() && year === today.getFullYear(),
                        });
                    }

                    // Next month days to fill grid
                    const remaining = 42 - days.length;
                    for (let d = 1; d <= remaining; d++) {
                        days.push({
                            key: `next-${d}`,
                            date: d,
                            currentMonth: false,
                            isToday: false,
                        });
                    }

                    return days;
                },

                calendarPrevMonth() {
                    this.calendarDate = new Date(this.calendarDate.getFullYear(), this.calendarDate.getMonth() - 1, 1);
                },

                calendarNextMonth() {
                    this.calendarDate = new Date(this.calendarDate.getFullYear(), this.calendarDate.getMonth() + 1, 1);
                },

                // ── Prayer Times ──
                prayer: {
                    nextName: null,
                    nextTime: null,
                    times: [],
                },

                async fetchPrayerTimes() {
                    try {
                        let url = '/api/prayer-times';
                        if (this.latitude !== null && this.longitude !== null) {
                            url += `?latitude=${this.latitude}&longitude=${this.longitude}`;
                        }
                        const res = await fetch(url);
                        if (res.ok) {
                            this.prayer = await res.json();
                            this.updateNextPrayer();
                        }
                    } catch {}
                },

                updateNextPrayer() {
                    if (!this.prayer || !this.prayer.times || this.prayer.times.length === 0) return;

                    const now = new Date();
                    const currentHours = now.getHours();
                    const currentMinutes = now.getMinutes();
                    const currentTimeVal = currentHours * 60 + currentMinutes;

                    let next = null;

                    // Convert HH:MM to minutes from midnight
                    const timesWithMinutes = this.prayer.times.map(t => {
                        const [h, m] = t.time.split(':').map(Number);
                        return {
                            name: t.name,
                            time: t.time,
                            minutes: h * 60 + m
                        };
                    });

                    // Find first prayer that is in the future today
                    for (const p of timesWithMinutes) {
                        if (p.minutes > currentTimeVal) {
                            next = p;
                            break;
                        }
                    }

                    // If all passed, it is the first prayer (Subuh) tomorrow
                    const isTomorrow = !next;
                    if (isTomorrow) {
                        next = timesWithMinutes[0];
                    }

                    // Update state
                    this.prayer.nextName = next.name;
                    this.prayer.nextTime = next.time;

                    this.prayer.times.forEach(t => {
                        t.isNext = (t.name === next.name) && !isTomorrow;
                    });
                },

                // ── Init ──
                init() {
                    // Request notification permission
                    if ('Notification' in window && Notification.permission === 'default') {
                        Notification.requestPermission();
                    }

                    // Load default playlist
                    this.currentPlaylist = this.playlists[0];

                    // Load data
                    this.loadTasks();
                    this.fetchQuote();
                    this.loadGamification();
                    this.initLocation();

                    // Update prayer highlights every 30 seconds
                    setInterval(() => {
                        this.updateNextPrayer();
                    }, 30000);
                },
            }
        }
    </script>

</body>
</html>
