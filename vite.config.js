import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import viteReact from "@vitejs/plugin-react";

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.jsx'],
            refresh: true,
        }),
        viteReact()
    ],
    build:{
        cssMinify:true,
        cssCodeSplit:true,
        force: true,
        rollupOptions: {
            output: {
                entryFileNames: '[name]-main.js',
                manualChunks(id) {
                    /*if (id.includes('react') || id.includes('react-dom') || id.includes('@babel')) {
                        return 'react';
                    }*/
                    if (id.includes('@shopify/polaris')) {
                        return 'polaris';
                    }
                    if (id.includes('lodash')) {
                        return 'lodash';
                    }

                    if (id.includes('react-color')) {
                        return 'react-color';
                    }

                    if (id.includes('node_modules')) {
                        return 'vendor';
                    }
                }
            }
        }
    }

});
