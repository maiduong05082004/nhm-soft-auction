// vite.config.js
import { defineConfig } from "file:///C:/laragon/www/nhm-soft-auction/node_modules/vite/dist/node/index.js";
import laravel, { refreshPaths } from "file:///C:/laragon/www/nhm-soft-auction/node_modules/laravel-vite-plugin/dist/index.js";
import tailwindcss from "file:///C:/laragon/www/nhm-soft-auction/node_modules/tailwindcss/lib/index.js";
import autoprefixer from "file:///C:/laragon/www/nhm-soft-auction/node_modules/autoprefixer/lib/autoprefixer.js";
var vite_config_default = defineConfig({
  plugins: [
    laravel({
      input: [
        "resources/css/app.css",
        "resources/js/app.js",
        "resources/js/partials/slide.js"
      ],
      refresh: [
        ...refreshPaths,
        "app/Filament/**",
        "app/Forms/Components/**",
        "app/Livewire/**",
        "app/Infolists/Components/**",
        "app/Providers/Filament/**",
        "app/Tables/Columns/**"
      ]
    })
  ],
  css: {
    postcss: {
      plugins: [
        tailwindcss,
        // Sử dụng require để gọi tailwindcss
        autoprefixer
      ]
    }
  }
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCJDOlxcXFxsYXJhZ29uXFxcXHd3d1xcXFxuaG0tc29mdC1hdWN0aW9uXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ZpbGVuYW1lID0gXCJDOlxcXFxsYXJhZ29uXFxcXHd3d1xcXFxuaG0tc29mdC1hdWN0aW9uXFxcXHZpdGUuY29uZmlnLmpzXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ltcG9ydF9tZXRhX3VybCA9IFwiZmlsZTovLy9DOi9sYXJhZ29uL3d3dy9uaG0tc29mdC1hdWN0aW9uL3ZpdGUuY29uZmlnLmpzXCI7aW1wb3J0IHsgZGVmaW5lQ29uZmlnIH0gZnJvbSAndml0ZSdcbmltcG9ydCBsYXJhdmVsLCB7IHJlZnJlc2hQYXRocyB9IGZyb20gJ2xhcmF2ZWwtdml0ZS1wbHVnaW4nXG5pbXBvcnQgdGFpbHdpbmRjc3MgZnJvbSAndGFpbHdpbmRjc3MnIC8vIFNcdTFFRUQgZFx1MUVFNW5nIGltcG9ydCB0aGF5IHZcdTAwRUMgcmVxdWlyZVxuaW1wb3J0IGF1dG9wcmVmaXhlciBmcm9tICdhdXRvcHJlZml4ZXInIC8vIFNcdTFFRUQgZFx1MUVFNW5nIGltcG9ydCB0aGF5IHZcdTAwRUMgcmVxdWlyZVxuZXhwb3J0IGRlZmF1bHQgZGVmaW5lQ29uZmlnKHtcbiAgICBwbHVnaW5zOiBbXG4gICAgICAgIGxhcmF2ZWwoe1xuICAgICAgICAgICAgaW5wdXQ6IFtcbiAgICAgICAgICAgICAgICAncmVzb3VyY2VzL2Nzcy9hcHAuY3NzJyxcbiAgICAgICAgICAgICAgICAncmVzb3VyY2VzL2pzL2FwcC5qcycsXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9qcy9wYXJ0aWFscy9tZW51LW1vYmlsZS5qcycsXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9qcy9wYXJ0aWFscy9zbGlkZS5qcydcbiAgICAgICAgICAgIF0sXG4gICAgICAgICAgICByZWZyZXNoOiBbXG4gICAgICAgICAgICAgICAgLi4ucmVmcmVzaFBhdGhzLFxuICAgICAgICAgICAgICAgICdhcHAvRmlsYW1lbnQvKionLFxuICAgICAgICAgICAgICAgICdhcHAvRm9ybXMvQ29tcG9uZW50cy8qKicsXG4gICAgICAgICAgICAgICAgJ2FwcC9MaXZld2lyZS8qKicsXG4gICAgICAgICAgICAgICAgJ2FwcC9JbmZvbGlzdHMvQ29tcG9uZW50cy8qKicsXG4gICAgICAgICAgICAgICAgJ2FwcC9Qcm92aWRlcnMvRmlsYW1lbnQvKionLFxuICAgICAgICAgICAgICAgICdhcHAvVGFibGVzL0NvbHVtbnMvKionLFxuICAgICAgICAgICAgXSxcbiAgICAgICAgfSksXG4gICAgXSxcbiAgICBjc3M6IHtcbiAgICAgICAgcG9zdGNzczoge1xuICAgICAgICAgICAgcGx1Z2luczogW1xuICAgICAgICAgICAgICAgIHRhaWx3aW5kY3NzLCAgLy8gU1x1MUVFRCBkXHUxRUU1bmcgcmVxdWlyZSBcdTAxMTFcdTFFQzMgZ1x1MUVDRGkgdGFpbHdpbmRjc3NcbiAgICAgICAgICAgICAgICBhdXRvcHJlZml4ZXIsXG4gICAgICAgICAgICBdLFxuICAgICAgICB9LFxuICAgIH0sXG59KVxuIl0sCiAgIm1hcHBpbmdzIjogIjtBQUF1UixTQUFTLG9CQUFvQjtBQUNwVCxPQUFPLFdBQVcsb0JBQW9CO0FBQ3RDLE9BQU8saUJBQWlCO0FBQ3hCLE9BQU8sa0JBQWtCO0FBQ3pCLElBQU8sc0JBQVEsYUFBYTtBQUFBLEVBQ3hCLFNBQVM7QUFBQSxJQUNMLFFBQVE7QUFBQSxNQUNKLE9BQU87QUFBQSxRQUNIO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsTUFDSjtBQUFBLE1BQ0EsU0FBUztBQUFBLFFBQ0wsR0FBRztBQUFBLFFBQ0g7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLE1BQ0o7QUFBQSxJQUNKLENBQUM7QUFBQSxFQUNMO0FBQUEsRUFDQSxLQUFLO0FBQUEsSUFDRCxTQUFTO0FBQUEsTUFDTCxTQUFTO0FBQUEsUUFDTDtBQUFBO0FBQUEsUUFDQTtBQUFBLE1BQ0o7QUFBQSxJQUNKO0FBQUEsRUFDSjtBQUNKLENBQUM7IiwKICAibmFtZXMiOiBbXQp9Cg==
