const { defineConfig } = require('@vue/cli-service');

module.exports = defineConfig({
  devServer: {
    host: '0.0.0.0',
    port: 8080,
    allowedHosts: 'all',
    webSocketServer: false,  // Disable WebSocket server
    proxy: {
      '/api': {
        target: 'http://php',
        changeOrigin: true,
        pathRewrite: { '^/api': '' },
      },
    },
  },
  transpileDependencies: true,
});
