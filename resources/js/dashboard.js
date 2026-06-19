export default function dashboard() {
    return {
        // ── Dark Mode ──
        darkMode: localStorage.getItem('darkMode') === 'true',

        toggleDarkMode() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('darkMode', this.darkMode);
        },

        currentTime: '',

        updateTime() {
            try {
                this.currentTime = new Date().toLocaleTimeString('en-US', {
                    hour: 'numeric',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false,
                    timeZone: 'Asia/Jakarta'
                });
            } catch (e) {
                this.currentTime = new Date().toLocaleTimeString('en-US', {
                    hour: 'numeric',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false
                });
            }
        },

        // ── Timezone / Greeting Helper ──
        getCurrentHour() {
            try {
                const formatter = new Intl.DateTimeFormat('en-US', {
                    hour: 'numeric',
                    hourCycle: 'h23',
                    timeZone: 'Asia/Jakarta'
                });
                return parseInt(formatter.format(new Date()), 10);
            } catch (e) {
                return new Date().getHours();
            }
        },

        // ── Greeting ──
        get greeting() {
            const h = this.getCurrentHour();
            if (h < 12) return 'Good Morning ☀️';
            if (h < 17) return 'Good Afternoon 🌤️';
            if (h < 20) return 'Good Evening 🌅';
            return 'Good Night 🌙';
        },

        get greetingShort() {
            const h = this.getCurrentHour();
            if (h < 12) return 'Good Morning';
            if (h < 17) return 'Good Afternoon';
            if (h < 20) return 'Good Evening';
            return 'Good Night';
        },

        get greetingEmoji() {
            const h = this.getCurrentHour();
            if (h < 12) return '☀️';
            if (h < 17) return '🌤️';
            if (h < 20) return '🌅';
            return '🌙';
        },

        get currentDate() {
            try {
                return new Date().toLocaleDateString('en-US', {
                    weekday: 'long',
                    month: 'long',
                    day: 'numeric',
                    year: 'numeric',
                    timeZone: 'Asia/Jakarta'
                });
            } catch (e) {
                return new Date().toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
            }
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

            // Get current hours and minutes in Asia/Jakarta timezone (matching backend prayer times)
            let currentHours, currentMinutes;
            try {
                const formatter = new Intl.DateTimeFormat('en-US', {
                    hour: 'numeric',
                    minute: 'numeric',
                    hourCycle: 'h23',
                    timeZone: 'Asia/Jakarta'
                });
                const parts = formatter.formatToParts(new Date());
                const hPart = parts.find(p => p.type === 'hour');
                const mPart = parts.find(p => p.type === 'minute');
                if (hPart && mPart) {
                    currentHours = parseInt(hPart.value, 10);
                    currentMinutes = parseInt(mPart.value, 10);
                } else {
                    throw new Error("Missing format parts");
                }
            } catch (e) {
                const now = new Date();
                currentHours = now.getHours();
                currentMinutes = now.getMinutes();
            }

            const currentTimeVal = currentHours * 60 + currentMinutes;
            const currentHHMM = `${String(currentHours).padStart(2, '0')}:${String(currentMinutes).padStart(2, '0')}`;

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

            // Check if any prayer time matches the current time
            this.prayer.times.forEach(p => {
                if (p.time === currentHHMM) {
                    const todayStr = new Intl.DateTimeFormat('en-US', { timeZone: 'Asia/Jakarta' }).format(new Date());
                    const notifyKey = `${p.name}-${todayStr}`;
                    if (localStorage.getItem('lastNotifiedPrayer') !== notifyKey) {
                        localStorage.setItem('lastNotifiedPrayer', notifyKey);
                        this.notifyPrayer(p.name);
                    }
                }
            });
        },

        playPrayerChime() {
            try {
                const Ctx = window.AudioContext || window.webkitAudioContext;
                const ctx = new Ctx();
                const tone = (freq, start, len, gain = 0.18) => {
                    const osc = ctx.createOscillator();
                    const g = ctx.createGain();
                    osc.connect(g);
                    g.connect(ctx.destination);
                    osc.frequency.value = freq;
                    osc.type = 'sine';
                    g.gain.setValueAtTime(0, start);
                    g.gain.linearRampToValueAtTime(gain, start + 0.08);
                    g.gain.exponentialRampToValueAtTime(0.0001, start + len);
                    osc.start(start);
                    osc.stop(start + len);
                };
                const t = ctx.currentTime;
                // A beautiful, peaceful arpeggio reminder (C-E-G-C)
                tone(523.25, t, 1.0, 0.2);       // C5
                tone(659.25, t + 0.25, 1.0, 0.18); // E5
                tone(783.99, t + 0.5, 1.2, 0.16);  // G5
                tone(1046.50, t + 0.75, 1.5, 0.14); // C6
            } catch {}
        },

        notifyPrayer(name) {
            this.playPrayerChime();

            if ('Notification' in window) {
                if (Notification.permission === 'granted') {
                    try {
                        new Notification('Waktu Sholat 🕌', {
                            body: `Sudah masuk waktu sholat ${name} untuk wilayah Anda.`,
                            tag: 'prayer-reminder',
                        });
                    } catch {}
                } else if (Notification.permission === 'default') {
                    Notification.requestPermission();
                }
            }
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

            // Update real-time clock every second
            this.updateTime();
            setInterval(() => {
                this.updateTime();
            }, 1000);

            // Update prayer highlights every 30 seconds
            setInterval(() => {
                this.updateNextPrayer();
            }, 30000);

            // Update weather every 5 minutes
            setInterval(() => {
                this.fetchWeather();
            }, 300000);
        },
    };
}
