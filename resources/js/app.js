import './bootstrap'
import ApexCharts from 'apexcharts'

// Import images to ensure they're processed by Vite
import '../images/chat-light.png'
import '../images/chat.png'
import '../images/mood-tracking.png'
import '../images/unified-interface.png'
import '../images/unified-interface-light.png'
import '../images/objectives.png'
import '../images/dashboard.png'
import '../images/hostages-board.png'
import '../images/hostages-cards.png'
import '../images/negotiations.png'

// Make ApexCharts available globally in multiple ways to ensure it works
window.ApexCharts = ApexCharts
globalThis.ApexCharts = ApexCharts

// Export ApexCharts for module usage
export { ApexCharts }

