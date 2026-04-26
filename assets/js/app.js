const observer = new IntersectionObserver((entries) => {
  entries.forEach((entry) => {
    if (entry.isIntersecting) {
      entry.target.classList.add('visible');
    }
  });
}, { threshold: 0.15 });

document.querySelectorAll('.reveal').forEach((el) => observer.observe(el));

const deliveryType = document.getElementById('delivery_type');
const addressWrap = document.getElementById('address_wrap');
if (deliveryType && addressWrap) {
  const toggleAddress = () => {
    addressWrap.style.display = deliveryType.value === 'delivery' ? 'block' : 'none';
  };
  deliveryType.addEventListener('change', toggleAddress);
  toggleAddress();
}
