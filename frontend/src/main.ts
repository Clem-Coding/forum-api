import "./assets/base.css";
import "./assets/main.css";

// import "./test-client";
// import "./test-auth";
// import "./test-users";

import { createApp } from "vue";
import App from "./App.vue";
import { router } from "./router";

const app = createApp(App);
app.use(router);
app.mount("#app");
