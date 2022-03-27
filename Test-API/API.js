let weather = {
    "apiKey" : "bf4aa9509addde9aed6170d99c22ed91",
    fetchWeather: function (city) {
        fetch(
        "https://api.openweathermap.org/data/2.5/weather?q="
        + city 
        + "&lang=fr"
        + "&units=metric"
        + "&appid="
        + this.apiKey   //"this" pour appeler l'objet apiKey
        )
        .then((response) => response.json())
        .then((data) => this.displayWeather(data));
    },
    //La fonction qui permet de recuperer les données de la ville recherché ainsi que de remplacer les valeurs dans le html
    displayWeather: function(data) {
        const { name } = data;  
        const { icon, description } = data.weather[0];  
        const { temp, humidity } = data.main;
        const { speed } = data.wind;
        console.log(name, icon, description, temp, humidity, speed)
        document.querySelector(".city").innerText = "La Météo à " + name;
        document.querySelector(".temp").innerText = temp + "°C";
        document.querySelector(".icon").src = "https://openweathermap.org/img/wn/" + icon + ".png";
        document.querySelector(".description").innerText = description;
        document.querySelector(".humidity").innerText ="humidité : "+ humidity + "%";
        document.querySelector(".wind").innerText ="vitesse du vent : "+ speed + "km/h";
    },
    search: function(){
       this.fetchWeather(document.querySelector(".search-bar").value);
    }
};

document.querySelector(".input button").addEventListener("click", function(){
    weather.search();
});



