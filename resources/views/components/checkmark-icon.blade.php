@props([
    'complete',
])

<svg
    xmlns="http://www.w3.org/2000/svg"
    fill="none"
    viewBox="0 0 24 24"
    stroke-width="1.5"
    stroke="currentColor"
    @class(['size-6', 'text-gray-800 hover:text-green-500 focus:text-green-500 dark:text-gray-200' => ! $complete, 'text-green-500 hover:text-gray-800 focus:text-gray-800 dark:hover:text-gray-200 dark:focus:text-gray-200' => $complete])
>
    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
</svg>
