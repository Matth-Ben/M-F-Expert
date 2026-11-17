/**
 * Classes per layout of the parent wpg/prices — mirror of render.php.
 * One markup for both layouts (icon, text, price — name read before price);
 * the grid places the price next to the icon on mobile / in cards,
 * and at the end of the row in the desktop list.
 */
const BASE = {
	item: 'grid grid-cols-[3.5rem_minmax(0,1fr)] items-center gap-x-3 gap-y-3.5 rounded-card bg-cream p-5.5',
	icon: 'icon-dot col-start-1 row-start-1 size-14 bg-bordeaux text-cream',
	text: 'col-span-2 row-start-2 flex flex-col gap-2',
	price: 'col-start-2 row-start-1 justify-self-end rounded-full bg-bordeaux px-4 py-2 text-sm leading-[1.7] font-extrabold text-cream',
};

export const CLASSES = {
	cards: {
		item: `${BASE.item} content-start lg:gap-y-5 lg:p-7`,
		icon: BASE.icon,
		text: `${BASE.text} lg:gap-2.5`,
		name: 'card-title',
		description: 'text-md leading-normal',
		price: BASE.price,
	},
	list: {
		item: `${BASE.item} lg:grid-cols-[3.5rem_minmax(0,1fr)_auto] lg:gap-x-5 lg:px-7 lg:py-6.5`,
		icon: BASE.icon,
		text: `${BASE.text} lg:col-span-1 lg:col-start-2 lg:row-start-1 lg:gap-0`,
		name: 'card-title lg:text-[1.25rem]',
		description: 'text-md leading-normal lg:text-note',
		price: `${BASE.price} lg:col-start-3`,
	},
};
