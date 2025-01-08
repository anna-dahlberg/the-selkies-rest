function updateTotalCost() {
    const arrival = new Date(document.getElementById('arrivalDate').value);
    const departure = new Date(document.getElementById('departureDate').value);
    const roomType = document.getElementById('roomType').value;
    
    if (!arrival.getTime() || !departure.getTime() || !roomType) {
        document.getElementById('totalCost').textContent = ''; // Display nothing
        return;
    }
    
    const days = Math.floor((departure - arrival) / (1000 * 60 * 60 * 24)) + 1;
    
    const roomPrices = {
        'budget': 3,
        'standard': 6,
        'luxury': 9
    };

    let total = days * roomPrices[roomType];
    
    const features = document.querySelectorAll('input[name="features[]"]:checked');
    total += features.length * 3;
    
    if (days >= 3) total -= 2;

    document.getElementById('totalCost').textContent = total;
}

['arrivalDate', 'departureDate', 'roomType'].forEach(id => {
    document.getElementById(id).addEventListener('change', updateTotalCost);
});

document.querySelectorAll('input[name="features[]"]').forEach(checkbox => {
    checkbox.addEventListener('change', updateTotalCost);
});