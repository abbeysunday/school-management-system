document.addEventListener('DOMContentLoaded', () => {
    // Mobile Sidebar Toggle
    const sidebar = document.getElementById('sidebar');
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const closeSidebarBtn = document.getElementById('close-sidebar-btn');
    const sidebarOverlay = document.getElementById('sidebar-overlay');

    function toggleSidebar() {
        sidebar.classList.toggle('-translate-x-full');
        sidebarOverlay.classList.toggle('hidden');
    }

    if(mobileMenuBtn) mobileMenuBtn.addEventListener('click', toggleSidebar);
    if(closeSidebarBtn) closeSidebarBtn.addEventListener('click', toggleSidebar);
    if(sidebarOverlay) sidebarOverlay.addEventListener('click', toggleSidebar);

    // Profile Dropdown Toggle
    const profileBtn = document.getElementById('profile-btn');
    const profileDropdown = document.getElementById('profile-dropdown');

    if(profileBtn) {
        profileBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            profileDropdown.classList.toggle('hidden');
        });
        document.addEventListener('click', () => {
            if(!profileDropdown.classList.contains('hidden')) {
                profileDropdown.classList.add('hidden');
            }
        });
    }
});

// ==========================================
// CBT Exam Security & Timer Logic
// ==========================================
function initCBTExam(durationMinutes) {
    let timeRemaining = durationMinutes * 60;
    const timerDisplay = document.getElementById('exam-timer');
    const progressBar = document.getElementById('exam-progress');

    // 1. Prevent Browser Back Button
    history.pushState(null, null, location.href);
    window.onpopstate = function () {
        history.go(1);
        alert("Action restricted! You cannot go back during an active exam.");
    };

    // 2. Prevent Refresh & Tab Close Warning
    window.addEventListener('beforeunload', function (e) {
        e.preventDefault();
        e.returnValue = "Are you sure you want to leave? Your exam will be submitted automatically.";
    });

    // 3. Prevent Copy/Paste/Right-Click
    document.addEventListener('contextmenu', event => event.preventDefault());
    document.addEventListener('copy', event => event.preventDefault());

    // 4. Timer Logic
    const timerInterval = setInterval(() => {
        timeRemaining--;

        let hours = Math.floor(timeRemaining / 3600);
        let minutes = Math.floor((timeRemaining % 3600) / 60);
        let seconds = timeRemaining % 60;

        timerDisplay.innerText =
            (hours > 0 ? (hours < 10 ? "0" + hours + ":" : hours + ":") : "") +
            (minutes < 10 ? "0" + minutes : minutes) + ":" +
            (seconds < 10 ? "0" + seconds : seconds);

        // Update Progress bar
        let percentage = ((durationMinutes * 60 - timeRemaining) / (durationMinutes * 60)) * 100;
        if(progressBar) progressBar.style.width = percentage + "%";

        // Warning color when < 5 minutes
        if (timeRemaining <= 300) {
            timerDisplay.classList.add('text-red-600', 'animate-pulse');
        }

        // Auto Submit
        if (timeRemaining <= 0) {
            clearInterval(timerInterval);
            window.onbeforeunload = null; // remove warning
            alert("Time is up! Submitting exam automatically...");
            window.location.href = "/student/results"; // Simulate submit redirect
        }
    }, 1000);
}
