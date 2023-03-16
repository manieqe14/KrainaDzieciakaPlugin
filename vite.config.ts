import { defineConfig } from 'vite';
import react from "@vitejs/plugin-react"

export default defineConfig({
        plugins: [react()],
        build: {
            rollupOptions: {
                output: {
                    entryFileNames: 'assets/[name].js',
                    assetFileNames: 'assets/[name].[ext]',
                }
            }
        },
        server: {
            port: 8001
        },
        resolve: {
            alias: {
                '@mui/styled-engine': '@mui/styled-engine-sc'
            }
        }
    }
);