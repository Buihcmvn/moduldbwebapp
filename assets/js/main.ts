import { createApp } from 'vue';
import { createPinia } from 'pinia';
import 'dhtmlx-gantt/codebase/dhtmlxgantt.css';

// Import các Vue Components
import LocalHardware from './components/LocalHardware.vue';
import EditHardware from './components/LocalHardwareEdit.vue';
import AddHardware from './components/LocalHardwareAdd.vue';
import SettingHardware from './components/LocalHardwareSetting.vue';
import SoftwareList from './components/SoftwareList.vue';
import SoftwareEdit from './components/SoftwareEdit.vue';
import SoftwareAdd from './components/SoftwareAdd.vue';
import ProjectTable from './components/ApplicationList.vue';
import ApplicationEdit from './components/ApplicationEdit.vue';
import ApplicationNew from './components/ApplicationNew.vue';
import Meilenstein from './components/Meilenstein.vue';
import AboutProject from './components/AboutPage.vue';

import router from './router';

// Import React mount function (theo chú thích trong ảnh)
import { mountBestellungApp } from './components/BestellungContext.jsx';

const pinia = createPinia();
const appRouter = router;

interface vueComponentConfig {
    component: any;
    selector: string;
}

const vueComponentsToMount: vueComponentConfig[] = [
    { component: LocalHardware, selector: "#localHardware" },
    { component: EditHardware, selector: "#editHardware" },
    { component: AddHardware, selector: "#addHardware" },
    { component: SettingHardware, selector: "#settingHardware" },
    
    { component: SoftwareList, selector: "#listSoftware" },
    { component: SoftwareEdit, selector: "#editSoftware" },
    { component: SoftwareAdd, selector: "#addSoftware" },
    
    { component: ProjectTable, selector: "#vueOptionApi" },
    { component: ApplicationEdit, selector: "#editProject" },
    { component: ApplicationNew, selector: "#addProject" },
    
    { component: AboutProject, selector: "#aboutProject" },
    { component: Meilenstein, selector: "#meilenstein" }
];

// Hàm để mount từng component vào DOM
function mountVueComponent({ component, selector }: vueComponentConfig) {
    const element = document.querySelector(selector);
    if (element) {
        createApp(component)
            .use(appRouter)
            .use(pinia)
            .mount(selector);
        console.log('Vue component mounted: ' + selector);
    }
}

// Lặp qua mảng để mount tất cả các Vue components
vueComponentsToMount.forEach(mountVueComponent);

// Khởi tạo React App nếu phần tử tồn tại
const reactElement = document.getElementById('bestellung-root');
if (reactElement) {
    mountBestellungApp(reactElement);
}