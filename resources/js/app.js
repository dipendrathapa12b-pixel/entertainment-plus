import './bootstrap';


window.scrollMedia = function (id, direction) {
    const slider = document.getElementById(id);

    if (!slider) return;

    slider.scrollBy({
        left: direction * 600,
        behavior: 'smooth'
    });
};
