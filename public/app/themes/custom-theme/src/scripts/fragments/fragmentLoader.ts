/**
 * Fragment Loader
 * 
 * Handles loading of PHP fragments via REST API with caching.
 * 
 */

export interface FragmentConfig {
	endpoint: string;
	nonce: string;
}

export interface LoadFragmentOptions {
	type: 'panel';
	key: string;
	props?: Record<string, any>;
}

export interface FragmentResponse {
	ok: boolean;
	html: string;
	key: string;
	type: string;
}

export class FragmentLoader {
	private config: FragmentConfig;
	private cache: Map<string, string> = new Map();

	constructor(config: FragmentConfig) {
		this.config = config;
	}

	/**
	 * Load a fragment from the REST API
	 */
	async load(options: LoadFragmentOptions): Promise<FragmentResponse> {
		const { type, key, props = {} } = options;
		
		// Check cache first
		const cacheKey = this.getCacheKey(type, key, props);
		const cached = this.cache.get(cacheKey);
		
		if (cached) {
			return {
				ok: true,
				html: cached,
				key,
				type,
			};
		}

		try {
			const response = await fetch(this.config.endpoint, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': this.config.nonce,
				},
				credentials: 'same-origin',
				body: JSON.stringify({
					type,
					key,
					props,
				}),
			});

			if (!response.ok) {
				const error = await response.json();
				throw new Error(error.message || `HTTP ${response.status}`);
			}

			const data: FragmentResponse = await response.json();

			// Cache the HTML
			if (data.ok && data.html) {
				this.cache.set(cacheKey, data.html);
			}

			return data;

		} catch (error) {
			console.error('[FragmentLoader] Error loading fragment:', error);
			throw error;
		}
	}

	/**
	 * Generate cache key
	 */
	private getCacheKey(type: string, key: string, props: Record<string, any>): string {
		const propsHash = JSON.stringify(props);
		return `${type}:${key}:${propsHash}`;
	}

	/**
	 * Clear cache
	 */
	clearCache(type?: string, key?: string): void {
		if (!type && !key) {
			this.cache.clear();
			return;
		}

		const keysToDelete: string[] = [];
		
		this.cache.forEach((_, cacheKey) => {
			const [cachedType, cachedKey] = cacheKey.split(':');
			
			if (type && cachedType === type) {
				if (!key || cachedKey === key) {
					keysToDelete.push(cacheKey);
				}
			}
		});

		keysToDelete.forEach(k => this.cache.delete(k));
	}

	/**
	 * Get cache size
	 */
	getCacheSize(): number {
		return this.cache.size;
	}
}

// Global instance
let loaderInstance: FragmentLoader | null = null;

/**
 * Initialize the fragment loader
 */
export function initFragmentLoader(config: FragmentConfig): FragmentLoader {
	if (!loaderInstance) {
		loaderInstance = new FragmentLoader(config);
	}
	return loaderInstance;
}

/**
 * Get the fragment loader instance
 */
export function getFragmentLoader(): FragmentLoader {
	if (!loaderInstance) {
		throw new Error('FragmentLoader not initialized. Call initFragmentLoader() first.');
	}
	return loaderInstance;
}

/**
 * Convenience function to load a fragment
 */
export async function loadFragment(options: LoadFragmentOptions): Promise<FragmentResponse> {
	const loader = getFragmentLoader();
	return loader.load(options);
}
