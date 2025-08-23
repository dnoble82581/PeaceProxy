<?php

	use Livewire\Volt\Component;

	new class extends Component {
		// Add any PHP methods here if needed
	}

?>

<div class="h-16 bg-white dark:bg-dark-800 border border-gray-300 dark:border-dark-600 mt-5 p-2 rounded-lg flex items-center justify-between"
     x-data="{
        showTimer: false,
        timerRunning: false,
        seconds: 0,
        minutes: 0,
        hours: 0,
        timerInterval: null,
        
        startTimer() {
            this.showTimer = true;
            this.timerRunning = true;
            this.seconds = 0;
            this.minutes = 0;
            this.hours = 0;
            
            this.timerInterval = setInterval(() => {
                this.seconds++;
                if (this.seconds >= 60) {
                    this.seconds = 0;
                    this.minutes++;
                    if (this.minutes >= 60) {
                        this.minutes = 0;
                        this.hours++;
                    }
                }
            }, 1000);
        },
        
        stopTimer() {
            this.timerRunning = false;
            clearInterval(this.timerInterval);
        },
        
        resetTimer() {
            this.stopTimer();
            this.showTimer = false;
        },
        
        formatTime(value) {
            return value.toString().padStart(2, '0');
        }
     }"
     @start-call-timer.window="startTimer()">
     
    <!-- Timer display - positioned on the far left -->
    <div x-show="showTimer" class="flex items-center space-x-2">
        <div class="text-dark-800 dark:text-white flex items-center">
            <span class="font-mono text-lg" x-text="formatTime(hours)"></span>
            <span class="mx-0.5">:</span>
            <span class="font-mono text-lg" x-text="formatTime(minutes)"></span>
            <span class="mx-0.5">:</span>
            <span class="font-mono text-lg" x-text="formatTime(seconds)"></span>
        </div>
        
        <div class="flex space-x-1">
            <button 
                @click="stopTimer()"
                class="px-2 py-1 bg-red-500 hover:bg-red-600 dark:bg-red-600 dark:hover:bg-red-700 text-white text-xs rounded-md transition-colors"
                x-show="timerRunning">
                Stop
            </button>
            <button 
                @click="resetTimer()"
                class="px-2 py-1 bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white text-xs rounded-md transition-colors"
                x-show="!timerRunning && showTimer">
                Reset
            </button>
        </div>
    </div>
    
    <!-- Default content when timer is not shown -->
    <div x-show="!showTimer" class="text-dark-800 dark:text-white font-medium">
        Notifications
    </div>
    
    <!-- Placeholder for other notification content -->
    <div class="text-dark-800 dark:text-white ml-auto" x-show="showTimer">
        <!-- Other notification content can go here -->
    </div>
</div>
