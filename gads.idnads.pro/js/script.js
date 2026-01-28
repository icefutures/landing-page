document.addEventListener('DOMContentLoaded', () => {
  const btnLock = document.getElementById('btnLock');
  const btnReject = document.getElementById('btnReject');
  const formLock = document.getElementById('formLock');
  const formReject = document.getElementById('formReject');
  const modal = document.getElementById('modal');
  const modalBox = document.getElementById('modalBox');

  btnLock.onclick = () => {
    formReject.classList.add('hidden');
    formReject.classList.remove('active');
    formLock.classList.remove('hidden');
    requestAnimationFrame(()=>formLock.classList.add('active'));
  };

  btnReject.onclick = () => {
    formLock.classList.add('hidden');
    formLock.classList.remove('active');
    formReject.classList.remove('hidden');
    requestAnimationFrame(()=>formReject.classList.add('active'));
  };

  function closeModal(){ modal.classList.add('hidden'); }

  function parseNumber(v){ return parseFloat(String(v).replace(/\./g,'')) || 0 }
  function format(n){ return Number(n).toLocaleString('id-ID') }

  nsbProfit.oninput = nsbModal.oninput = () => {
    const modal = parseNumber(nsbModal.value);
    const profit = parseFloat(nsbProfit.value) || 0;
    const hasil = modal + (modal * profit / 100);
    nsbHasil.value = format(hasil);
  }

  // kode untuk btnLockProcess dan rejDetail juga ikut dipindah
});
