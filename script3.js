const months = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December",
];
const daysInMonth = [31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
const daysOfWeek = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];


const eachDay = document.querySelectorAll(".day");
var nextButton = document.querySelector(".next__btn");
var prevButton = document.querySelector(".prev__btn");


const currentDate = new Date();
var dayOfMonth = currentDate.getDate();
var dayOfWeek = currentDate.getDay();
var month = currentDate.getMonth();

function updateDates(){

    eachDay.forEach(function(aDay, i){ //goes through each day of the week
        var dayInput = dayOfMonth - dayOfWeek + i; 
        var monthInput = month;            
        console.log(monthInput);

        if(dayInput < 1 && month != 0){ //checks if sunday is before 1st day of month and current month is not January
            dayInput = daysInMonth[month - 1] + dayInput;
            monthInput = month - 1;
        }else if(dayInput < 1){ //checks if sunday is before 1st day of month
            dayInput = daysInMonth[11] + dayOfMonth - dayOfWeek;
            monthInput = 11;
            console.log(daysInMonth[11]);
            console.log(dayOfMonth);
            console.log(dayOfWeek);
            console.log(" jan");
        }else if(dayInput > daysInMonth[month] && month != 11){ //if date is greater then the number of days in that month and not december
            dayInput = dayInput - daysInMonth[month];
            monthInput = month + 1;
        }else if(dayInput > daysInMonth[month]){ //if date is greater then the number of days in that month and not december
            dayInput = dayInput - daysInMonth[0];
            monthInput = 0;
        }
        
        aDay.textContent = daysOfWeek[i] + " " + months[monthInput] + " " + dayInput;

    });

}

updateDates();

nextButton.addEventListener("click", function(){
    dayOfMonth += 7;
    if(dayOfMonth > daysInMonth[month] && month != 11){
        dayOfMonth = dayOfMonth - daysInMonth[month];
        month += 1;
    }else if(dayOfMonth > daysInMonth[month]){
        dayOfMonth = dayOfMonth - daysInMonth[month];
        month = 0;
    }
    updateDates();
});

prevButton.addEventListener("click", function(){
    dayOfMonth -= 7;
    if(dayOfMonth < 1 && month != 0){
        dayOfMonth = daysInMonth[month - 1] + dayOfMonth;
        month -= 1;
    }else if(dayOfMonth < 1){
        dayOfMonth = daysInMonth[month - 1] + dayOfMonth;
        month = 11;
    }
    updateDates();
});



/*add class*/

const classFormBtn = document.querySelector(".add__class__btn");
const addClassForm = document.querySelector(".add__class");

const backdrop = document.querySelector(".backdrop");
const whiteBackdrop = document.querySelector(".class__backdrop");

const toggleAddClassForm = () => {
    addClassForm.classList.toggle("active");
    backdrop.classList.toggle("active");
    classFormBtn.classList.toggle("active");
    whiteBackdrop.classList.toggle("active");
};

classFormBtn.addEventListener("click", toggleAddClassForm);


/*Create New Class*/

const addClassBtn = document.querySelector(".add__btn");
const classesContainer = document.querySelector(".classes__container");
const classInput = document.getElementById("class__input");
const categorySelect = document.getElementById("category__select");
const timeInput = document.getElementById("time__input");
const locationSelect = document.getElementById("location__select");
const spotsInput = document.getElementById("spots__input");


function updateActiveState() {
    eachDay.forEach(day => {
        day.addEventListener("click", () => {
            eachDay.forEach(day => day.classList.remove("active"));
            day.classList.add("active");
            displayClasses(day.textContent);
            loadClasses();
        });
    });
}

function displayClasses(day) {
    const allClasses = document.querySelectorAll(".class");
    allClasses.forEach(cls => {
        if (cls.dataset.day === day) {
            cls.style.display = "flex";
        } else {
            cls.style.display = "none";
        }
    });
}

function createClassElement(className, category, time, location, spots, day) {
    const newClass = document.createElement("div");
    newClass.classList.add("class");
    newClass.dataset.day = day;

    newClass.innerHTML = `
        <div class="class__time">${time}</div>
        <div class="class__details">
            <div class="class__name">${className}</div>
            <div class="class__category">${category}</div>
        </div>
        <div class="class__location">${location}</div>
        <div class="class__details">
            <button class="book__btn">Book</button>
            <div class="class__spots">${spots} spots left</div>
        </div>
    `;
    return newClass;
}

function handleAddButtonClick(event) {
    event.preventDefault();

    const className = classInput.value;
    const category = categorySelect.value;
    const time = timeInput.value;
    const location = locationSelect.value;
    const spots = spotsInput.value;
    const day = document.querySelector(".day.active").textContent; 

    fetch("saveClasses.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            className: className,
            category: category,
            time: time,
            location: location,
            spots: spots,
            day: day 
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error("Failed to add class.");
        }
        return response.text();
    })
    .then(data => {
        console.log(data); 
        loadClasses(); 
    })
    .catch(error => {
        console.error("Error:", error);
    });
}

function displayClasses(day) {
    const allClasses = document.querySelectorAll(".class");
    allClasses.forEach(cls => {
        if (cls.dataset.day === day) { 
            cls.style.display = "flex";
        } else {
            cls.style.display = "none";
        }
    });
}

addClassBtn.addEventListener("click", handleAddButtonClick);

function loadClasses() {
    const activeDay = document.querySelector(".day.active").textContent; 
    fetch(`loadClasses.php?date=${activeDay}`) 
    .then(response => response.json())
    .then(classes => {
        // Clear existing classes
        classesContainer.innerHTML = "";
        // Add newly loaded classes
        classes.forEach(cls => {
            const [day, time, className, category, location, spots] = cls;
            const newClassElement = createClassElement(className, category, time, location, spots, day);
            classesContainer.appendChild(newClassElement);
        });
    })
    .catch(error => {
        console.error("Error:", error);
    });
}

window.addEventListener("DOMContentLoaded", () => {
    loadClasses();
});


addClassBtn.addEventListener("click", handleAddButtonClick);
addClassBtn.addEventListener("click", toggleAddClassForm);

updateActiveState();
