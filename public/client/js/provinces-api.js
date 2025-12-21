/**
 * Tải provinces, communes từ Cas AddressKit API
 * API: https://addresskit.cas.so/
 * Format: 2-level address (Tỉnh - Xã, không có Quận)
 * Auto-fill địa chỉ khi chọn tỉnh và xã
 */

document.addEventListener('DOMContentLoaded', function() {
    const provinceSelect = document.getElementById('province');
    const communeSelect = document.getElementById('commune');
    const cityInput = document.querySelector('input[name="city"]');
    const districtInput = document.querySelector('input[name="district"]');
    const addressInput = document.querySelector('input[name="address"]');

    if (!provinceSelect) return; // Không phải trang checkout

    let selectedProvinceName = '';
    let selectedCommuneName = '';

    // Helper: Normalize response data
    function normalizeData(response) {
        if (response.data && Array.isArray(response.data)) {
            return response.data;
        } else if (Array.isArray(response)) {
            return response;
        }
        return [];
    }

    // Update address field
    function updateAddressField() {
        if (selectedProvinceName && selectedCommuneName) {
            addressInput.value = `${selectedCommuneName}, ${selectedProvinceName}`;
        }
    }

    // ====== LOAD PROVINCES ======
    async function loadProvinces() {
        try {
            provinceSelect.innerHTML = '<option value="">Đang tải tỉnh/thành phố...</option>';
            const response = await fetch('/api/provinces');
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            
            const json = await response.json();
            
            if (json.error) {
                throw new Error(json.error);
            }
            
            const provinces = normalizeData(json);
            
            if (!provinces || provinces.length === 0) {
                throw new Error('Không có dữ liệu tỉnh/thành phố');
            }

            provinceSelect.innerHTML = '<option value="">-- Chọn Tỉnh/Thành phố --</option>';
            provinces.forEach(province => {
                const option = document.createElement('option');
                option.value = province.code || province.id;
                option.textContent = province.name;
                option.dataset.name = province.name;
                provinceSelect.appendChild(option);
            });

            // Restore old value if exists
            if (cityInput && cityInput.value) {
                const matching = Array.from(provinceSelect.options).find(opt => 
                    opt.dataset.name === cityInput.value
                );
                if (matching) {
                    provinceSelect.value = matching.value;
                    provinceSelect.dispatchEvent(new Event('change'));
                }
            }

            document.getElementById('province-status').textContent = `✓ ${provinces.length} tỉnh/thành phố`;
        } catch (error) {
            console.error('Error loading provinces:', error);
            provinceSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
            document.getElementById('province-status').textContent = '❌ ' + error.message;
        }
    }

    // ====== LOAD COMMUNES/WARDS ======
    async function loadCommunes(provinceID) {
        try {
            communeSelect.innerHTML = '<option value="">Đang tải phường/xã...</option>';
            communeSelect.disabled = true;
            
            const response = await fetch(`/api/communes?provinceID=${provinceID}`);
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            
            const json = await response.json();
            
            if (json.error) {
                throw new Error(json.error);
            }
            
            const communes = normalizeData(json);
            
            if (!communes || communes.length === 0) {
                throw new Error(`Không có dữ liệu phường/xã`);
            }

            communeSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
            communes.forEach(commune => {
                const option = document.createElement('option');
                option.value = commune.code || commune.id;
                option.textContent = commune.name;
                option.dataset.name = commune.name;
                communeSelect.appendChild(option);
            });
            
            communeSelect.disabled = false;
            document.getElementById('commune-status').textContent = `✓ ${communes.length} phường/xã`;

            // Restore old value if exists
            if (districtInput && districtInput.value) {
                const matching = Array.from(communeSelect.options).find(opt => 
                    opt.dataset.name === districtInput.value
                );
                if (matching) {
                    communeSelect.value = matching.value;
                    communeSelect.dispatchEvent(new Event('change'));
                }
            }
        } catch (error) {
            console.error('Error loading communes:', error);
            communeSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
            document.getElementById('commune-status').textContent = '❌ ' + error.message;
        }
    }

    // ====== EVENT LISTENERS ======
    provinceSelect.addEventListener('change', function() {
        if (!this.value) {
            communeSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
            communeSelect.disabled = true;
            cityInput.value = '';
            districtInput.value = '';
            selectedProvinceName = '';
            selectedCommuneName = '';
            addressInput.value = '';
            document.getElementById('commune-status').textContent = '';
            return;
        }

        selectedProvinceName = this.options[this.selectedIndex].dataset.name;
        cityInput.value = selectedProvinceName;
        districtInput.value = '';
        selectedCommuneName = '';
        
        communeSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
        communeSelect.disabled = true;
        addressInput.value = '';
        loadCommunes(this.value);
    });

    communeSelect.addEventListener('change', function() {
        if (!this.value) {
            districtInput.value = '';
            selectedCommuneName = '';
            addressInput.value = '';
            return;
        }
        selectedCommuneName = this.options[this.selectedIndex].dataset.name;
        districtInput.value = selectedCommuneName;
        updateAddressField();
    });

    // ====== INITIAL LOAD ======
    loadProvinces();
});

