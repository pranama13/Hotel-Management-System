// Guest Detail Panel Functions
const detailpanel = document.getElementById("guestdetailpanel");

const adduseropen = () => {
    if (detailpanel) {
        detailpanel.style.display = "flex";
    } else {
        console.error("Guest detail panel element not found!");
    }
};

const adduserclose = () => {
    if (detailpanel) {
        detailpanel.style.display = "none";
    } else {
        console.error("Guest detail panel element not found!");
    }
};

// Search Functionality
const searchFun = () => {
    const filter = document.getElementById('search_bar')?.value.toUpperCase() || '';
    const myTable = document.getElementById("table-data");

    if (!myTable) {
        console.error("Table element not found!");
        return;
    }

    const tr = myTable.getElementsByTagName('tr');

    for (let i = 0; i < tr.length; i++) {
        const td = tr[i].getElementsByTagName('td')[1];

        if (td) {
            const textValue = td.textContent || td.innerHTML;
            tr[i].style.display = textValue.toUpperCase().includes(filter) ? "" : "none";
        }
    }
};

// Price Calculation Function
const updatePrice = () => {
    const roomType = document.getElementById('roomType');
    const bedType = document.getElementById('bedType');
    const noOfRooms = document.getElementById('noOfRooms');
    const mealType = document.getElementById('mealType');
    const checkin = document.getElementById('checkin');
    const checkout = document.getElementById('checkout');

    const roomTotalSpan = document.getElementById('roomTotal');
    const bedTotalSpan = document.getElementById('bedTotal');
    const mealTotalSpan = document.getElementById('mealTotal');
    const finalTotalSpan = document.getElementById('finalTotal');

    if (!roomType || !bedType || !noOfRooms || !mealType || !checkin || !checkout || 
        !roomTotalSpan || !bedTotalSpan || !mealTotalSpan || !finalTotalSpan) {
        console.error("One or more elements not found!");
        return;
    }

    if (roomType.value && bedType.value && noOfRooms.value && mealType.value && checkin.value && checkout.value) {
        const roomPrice = parseFloat(roomType.options[roomType.selectedIndex].getAttribute('data-price'));
        const bedPrice = parseFloat(bedType.options[bedType.selectedIndex].getAttribute('data-price'));
        const mealPrice = parseFloat(mealType.options[mealType.selectedIndex].getAttribute('data-price'));
        const rooms = parseInt(noOfRooms.value) || 1;

        const checkinDate = new Date(checkin.value);
        const checkoutDate = new Date(checkout.value);

        if (checkoutDate <= checkinDate) {
            alert("Check-out date must be after check-in date!");
            return;
        }

        const days = Math.ceil((checkoutDate - checkinDate) / (1000 * 60 * 60 * 24));

        if (days > 0) {
            const roomTotal = roomPrice * days * rooms;
            const bedTotal = bedPrice * days * rooms;
            const mealTotal = mealPrice * days * rooms;
            const finalTotal = roomTotal + bedTotal + mealTotal;

            roomTotalSpan.textContent = roomTotal.toFixed(2);
            bedTotalSpan.textContent = bedTotal.toFixed(2);
            mealTotalSpan.textContent = mealTotal.toFixed(2);
            finalTotalSpan.textContent = finalTotal.toFixed(2);
        } else {
            roomTotalSpan.textContent = '0';
            bedTotalSpan.textContent = '0';
            mealTotalSpan.textContent = '0';
            finalTotalSpan.textContent = '0';
        }
    }
};