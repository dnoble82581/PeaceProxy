import './bootstrap'
import '../../vendor/tallstackui/tallstackui/dist/tallstackui-kK-3uhcG.js'
import ApexCharts from 'apexcharts'

// Make ApexCharts available globally in multiple ways to ensure it works
window.ApexCharts = ApexCharts
globalThis.ApexCharts = ApexCharts

// Export ApexCharts for module usage
export { ApexCharts }

