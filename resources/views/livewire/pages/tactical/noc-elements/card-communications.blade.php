<?php

	use Livewire\Volt\Component;

	new class extends Component {
		// Static table for now; replace with dynamic channels when ready
	};

?>

<x-card
		class="h-[15rem]"
		header="Communications">
	<div class="flow-root">
		<div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
			<div class="inline-block min-w-full align-middle sm:px-4 lg:px-6">
				<div class="overflow-hidden shadow-sm outline-1 outline-black/5 sm:rounded-lg dark:shadow-none dark:-outline-offset-1 dark:outline-white/10">
					<table class="relative min-w-full divide-y divide-gray-300 dark:divide-white/15">
						<thead class="bg-dark-50 dark:bg-dark-800/75">
						<tr>
							<th
									scope="col"
									class="py-1 pr-3 pl-4 text-left text-xs font-semibold text-gray-900 sm:pl-2 dark:text-dark-200">
								Id
							</th>
							<th
									scope="col"
									class="py-1 pr-3 pl-4 text-left text-xs font-semibold text-gray-900 sm:pl-2 dark:text-dark-200">
								Function
							</th>
							<th
									scope="col"
									class="py-1 pr-3 pl-4 text-left text-xs font-semibold text-gray-900 sm:pl-2 dark:text-dark-200">
								Command Frequency
							</th>
							<th
									scope="col"
									class="py-1 pr-3 pl-4 text-left text-xs font-semibold text-gray-900 sm:pl-2 dark:text-dark-200">
								Tactical Frequency
							</th>
						</tr>
						</thead>
						<tbody class="divide-y divide-dark-200 bg-white dark:divide-white/10 dark:bg-dark-800/50">
						<tr>
							<td class="py-2 pr-3 pl-4 text-xs font-medium whitespace-nowrap text-gray-900 sm:pl-6 dark:text-white">
								1
							</td>
							<td class="px-1 py-2 text-xs whitespace-nowrap text-gray-500 dark:text-gray-400">General
							                                                                                 Comms
							</td>
							<td class="px-1 py-2 text-xs whitespace-nowrap text-gray-500 dark:text-gray-400">RTAC Call
							                                                                                 62
							</td>
							<td class="px-1 py-2 text-xs whitespace-nowrap text-gray-500 dark:text-gray-400">RTAC Call
							                                                                                 61
							</td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</x-card>
