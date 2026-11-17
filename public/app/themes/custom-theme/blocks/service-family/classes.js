/**
 * Classes per layout of the parent wpg/services — mirror of render.php.
 */
export const CLASSES = {
	grid: {
		card: 'relative flex flex-col gap-4 rounded-panel bg-cream px-6.5 py-7.5 lg:px-8 lg:py-10',
		body: 'flex flex-col gap-4',
		text: '',
		badge: 'absolute -top-3.5 -right-1.5 size-[7.375rem] lg:-top-5.5 lg:-right-4.5 lg:size-[8.25rem]',
	},
	detailed: {
		card: 'relative flex flex-col gap-4 rounded-panel bg-cream px-6.5 py-7.5 lg:col-span-2 lg:flex-row lg:items-center lg:gap-7 lg:px-10 lg:py-7.5',
		body: 'flex flex-col gap-4 lg:flex-1 lg:gap-2',
		text: 'lg:max-w-[40rem]',
		badge: 'absolute -top-3.5 -right-1.5 size-[7.375rem] lg:relative lg:top-auto lg:right-auto lg:-my-3.5 lg:size-[8.25rem]',
	},
};
