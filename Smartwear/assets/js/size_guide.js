  // Size Guide Popup Functions
function openSizeGuidePopup() {
    document.getElementById('sizeGuidePopup').classList.add('active');
}

function closeSizeGuidePopup() {
    document.getElementById('sizeGuidePopup').classList.remove('active');
}

// Add event listener for the size guide link
document.addEventListener('DOMContentLoaded', function() {
    const sizeGuideLink = document.getElementById('openSizeGuide');
    if (sizeGuideLink) {
        sizeGuideLink.addEventListener('click', function(e) {
            e.preventDefault();
            openSizeGuidePopup();
        });
    }
});