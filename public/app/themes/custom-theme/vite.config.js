/**
 * Vite — global front-end assets (Tailwind CSS, app JS, editor canvas CSS).
 *
 * Gutenberg blocks are compiled separately by @wordpress/scripts (see webpack.config.js).
 */
import { defineConfig, loadEnv } from "vite";
import laravel from "laravel-vite-plugin";
import viteSvgSpriteWrapper from "vite-svg-sprite-wrapper";
import viteCompression from "vite-plugin-compression";
import tailwindcss from "@tailwindcss/vite";
import fs from "node:fs";
import path from "node:path";
import { fileURLToPath } from "node:url";

const themeDir = path.dirname(fileURLToPath(import.meta.url));
const projectDir = path.resolve(themeDir, "../../../..");
const certDir = path.join(themeDir, ".vite-certs");

export default defineConfig(({ mode }) => {
	// WP_HOME comes from the project's root .env (same file as wp-config.php).
	const env = loadEnv(mode, projectDir, "");
	const hasCerts = fs.existsSync(path.join(certDir, "key.pem"));

	return {
		base: "",
		build: {
			manifest: true,
			outDir: "dist",
			assetsDir: "src",
		},
		server: {
			https: hasCerts
				? {
						key: fs.readFileSync(path.join(certDir, "key.pem")),
						cert: fs.readFileSync(path.join(certDir, "cert.pem")),
					}
				: undefined,
			host: "0.0.0.0",
			port: 5173,
			strictPort: true,
			cors: {
				origin: [env.WP_HOME || "https://wp-gutenberg.local"],
				credentials: true,
			},
			hmr: {
				host: "localhost",
				protocol: hasCerts ? "wss" : "ws",
			},
		},
		plugins: [
			laravel({
				publicDirectory: "dist",
				input: ["src/stylesheets/styles.css", "src/scripts/app.js", "src/editor/editor.css"],
				// Full reload when a template, a block render.php or a block build changes.
				refresh: ["**/*.php", "build/**/*.js"],
			}),
			tailwindcss(),
			viteSvgSpriteWrapper({
				icons: "src/icons/*.svg",
				outputDir: "public",
			}),
			viteCompression(),
		],
	};
});
