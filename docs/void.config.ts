import { defineConfig } from 'void/config';

export default defineConfig({
	cloudflare: {
		compatibility_date: '2026-11-01',
		name: 'craft-stack',
		routes: [{ custom_domain: true, pattern: 'craft-stack.somehow-digital.dev' }],
	},
});
