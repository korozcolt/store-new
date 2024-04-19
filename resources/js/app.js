import './bootstrap';
import 'preline';

document.addEventListener('livewire:navigated', function() {
    window.HSStaticMethods.autoInit();
});
