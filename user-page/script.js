document.getElementById("create-reminder-btn").addEventListener("click", Form_Display_Toggle);
document.getElementById("hide-form").addEventListener("click", Form_Display_Toggle);

function Load_Tasks(){
    //FETCH DATA FROM DATABASE
    fetch("fetch-user-tasks.php")
    .then(response => {
        if (!response.ok) {
            throw new Error("HTTP error! Status: " + response.status);
        }
        return response.text(); // Read response as text once
    })
    .then(text => {
        try {
            let data = JSON.parse(text); // Try to parse JSON
            if (data.error) {
                document.body.innerHTML = `<h2 style='color: red; text-align: center;'>${data.error}</h2>`;
            } else {
                displayTasks(data);
            }
        } catch (error) {
            // If JSON parsing fails, assume it's an HTML error message
            document.body.innerHTML = text;
        }
    })
    .catch(error => {
        document.body.innerHTML = `<h2 style='color: red; text-align: center;'>Error: ${error.message}</h2>`;
    });
}

document.addEventListener("DOMContentLoaded", Load_Tasks);

function displayTasks(tasks) {
    const current_taskContainer = document.getElementById("current-tasks");
    const expired_taskContainer = document.getElementById("expired-tasks");
    const completed_taskContainer = document.getElementById("completed-tasks")
    current_taskContainer.innerHTML = ""; // Clear existing tasks
    expired_taskContainer.innerHTML = "";
    completed_taskContainer.innerHTML = "";
    
    

    let tasks_to_do_available = false;
    let expired_tasks_available = false;
    let completed_tasks_available = false;
    

    tasks.forEach(task => {

        
        let random_color = getRandomColor();
        let task_expired = isTaskExpired(task.expiry_date);
        task.expiry_date = task.expiry_date === new Date().toISOString().split("T")[0] ? "Today" : task.expiry_date;

        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(today.getDate() + 1); 
        const formattedTomorrow = tomorrow.toISOString().split("T")[0]; 
        task.expiry_date = task.expiry_date === formattedTomorrow ? "Tomorrow" : task.expiry_date;


        const yesterday = new Date(today);
        yesterday.setDate(today.getDate() - 1); 
        const formatted_yesterday = tomorrow.toISOString().split("T")[0]; 
        task.expiry_date = task.expiry_date === formatted_yesterday ? "Yesterday" : task.expiry_date;

        if(task.task_completed){
            let tagsHTML = task.tags.map(tag => `<p id="task-tag">${tag}</p>`).join("");
            let taskElement = `
                <div id="task">
                        <div id="completed-circle" style="border-color:${random_color}; background-color:${random_color};">
                        <span class="completed-checkmark">&#10004</span>
                        </div>
                        <div id="completed-task-info">
                            <p id="completed-task-title">${task.title}</p>
                            <p id="completed-task-expiry">${task.expiry_date}</p>
                        </div>
                        <div id="completed-tags">
                        ${tagsHTML}
                        </div>
                    </div>
                `;
                completed_tasks_available = true;
            completed_taskContainer.innerHTML += taskElement;
        } else if (task_expired ){
            let tagsHTML = task.tags.map(tag => `<p id="expired-task-tag">${tag}</p>`).join("");
            let taskElement = `
                <div id="expired-task">
                        <div id="expired-circle" style="border-color:${random_color}; background-color:${random_color};">
                            <span class="expired-checkmark">&#x274C;</span>
                        </div>
                        <div id="task-info">
                            <p id="expired-task-title">${task.title}</p>
                            <p id="expired-task-expiry">${task.expiry_date}</p>
                        </div>
                        <div id="expired-tags">
                        ${tagsHTML}
                        </div>
                    </div>
                `;
            expired_tasks_available = true;
            expired_taskContainer.innerHTML = taskElement + expired_taskContainer.innerHTML;
        }else if(!task_expired){
            let tagsHTML = task.tags.map(tag => `<p id="task-tag">${tag}</p>`).join("");
            let taskElement = `
                <div id="task">
                        <div id="circle" style="border-color:${random_color};" onclick="Toggle_Mark_Form_Show('${task.task_id}', '${task.title}')"></div>
                        <div id="task-info">
                            <p id="task-title">${task.title}</p>
                            <p id="task-expiry">${task.expiry_date}</p>
                        </div>
                        <div id="tags">
                        ${tagsHTML}
                        </div>
                    </div>
                `;
                tasks_to_do_available = true;
            current_taskContainer.innerHTML += taskElement;
        } 
        
        

    });

    current_taskContainer.innerHTML = tasks_to_do_available ?  current_taskContainer.innerHTML: "<p class=\"no-task\">No tasks available</p>";
    expired_taskContainer.innerHTML = expired_tasks_available ?  expired_taskContainer.innerHTML: "<p class=\"no-task\">No expired tasks</p>";
    completed_taskContainer.innerHTML = completed_tasks_available ?  completed_taskContainer.innerHTML: "<p class=\"no-task\">No completed tasks</p>";

}


document.getElementById("form").addEventListener("submit", function(event) {
    event.preventDefault(); // Prevent form from reloading the page

    const formData = new FormData(this);

    fetch("add-task.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json()) // Convert response to JSON
    .then(data => {
        if (data.success) {
            this.reset();
            Form_Display_Toggle();
            Load_Tasks(); // Refresh tasks
        } else {
            console.error("Task creation failed:", data.message);
        }
    })
    .catch(error => console.error("Task creation error:", error));
});


function Form_Display_Toggle(){
    document.getElementById("create-reminder-form").classList.toggle("show");
    document.getElementById("form").classList.toggle("show");
}


const today = new Date().toISOString().split("T")[0];
    
    // Set the min attribute to today
document.getElementById("create-task-expiry").setAttribute("min", today);


/*document.getElementById("form").addEventListener("submit", function (event){
    event.preventDefault();
});*/


function getRandomColor() {
    const letters = "0123456789ABCDEF";
    let color = "#";
    for (let i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}


function isTaskExpired(expiryDate) {
    const today = new Date().toISOString().split("T")[0]; // Get today's date in YYYY-MM-DD format
    return expiryDate < today; // Returns true if the task is expired
}

document.addEventListener("DOMContentLoaded", function(){
    document.getElementById("current-date").textContent = new Date().toISOString().split("T")[0];

    document.getElementById("complete-mark").addEventListener("click", function () {
        let task_id = document.getElementById("mark-as-completed").dataset.taskId;
        Mark_As_Completed(task_id);
    });

    document.getElementById("hide-mark").addEventListener("click", function () {
        document.getElementById("mark-as-completed").classList.remove("show");
        });
});


function Toggle_Mark_Form_Show(task_id, task_title){
    document.getElementById("task-to-mark").textContent = task_title; // Display task title
    document.getElementById("mark-as-completed").dataset.taskId = task_id; // Store task ID
    document.getElementById("mark-as-completed").classList.add("show"); // Show form

    
}

function Mark_As_Completed(task_id){
    fetch("mark-task-as-completed.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ task_id: task_id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById("mark-as-completed").classList.remove("show"); // Hide form
            Load_Tasks(); // Refresh tasks
        } else {
            alert("Error: " + data.error);
        }
    })
    .catch(error => console.error("Error:", error));
}

