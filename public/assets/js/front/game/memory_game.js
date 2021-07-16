let emogisArray = [
    "fa-smile",
    "fa-grin-stars",
    "fa-grin-tongue",
    "fa-grin-beam",
    "fa-frown",
    "fa-grin-squint-tears",
    "fa-angry",
    "fa-dizzy",
    "fa-grin-wink",
    "fa-meh",
    "fa-meh-blank",
    "fa-sad-tear",
  ];
  let date_stop;
  let cardsChosen = [];
  let cardsMatched = [];
  const resultDisplay = document.querySelector("#result");

  $( "#game-container" ).one( "click", function() { 
    date_start = Date.now();
  });

  // Func para crear el grid completo
  function createGrid() {
    const container = document.getElementById("game-container");
    let allEmogis = emogisArray
      .concat(emogisArray)
      .sort(() => 0.5 - Math.random());
  
    allEmogis.forEach((item) => {
      const card = document.createElement("div");
      card.classList.add("card");
      card.setAttribute("data-name", `${item}`);
      card.addEventListener("click", displayCard);

      const contentCard = document.createElement("i");
      contentCard.classList.add("far", `${item}`);
      card.append(contentCard);
  
      container.appendChild(card);
    });
  }
  
  // Func para mostrar una tarjeta
  function displayCard() {
    let cardSelected = this;
    cardSelected.classList.add("selected", "disabled");
    cardsChosen.push(cardSelected);
  
    if (cardsChosen.length === 2) {
      checkForMatch();
    }
  }
  
  // Func para deshabilitar la posibilidad de hacer click
  // cuando ya hay dos tarjetas seleccionadas
  function disableCardsTemporaly() {
    const noSelected = document.querySelectorAll(".card");
    noSelected.forEach((card) => {
      card.classList.add("disabled");
    });
  }
  
  // Func para activar nuevamente la posibilidad de hacer click
  function enableCards() {
    const allCards = document.querySelectorAll(".card");
    allCards.forEach((card) => {
      card.classList.remove("disabled");
    });
  }
  
  // Func para comprobar si hay coincidencia
  function checkForMatch() {
    disableCardsTemporaly();
    if (cardsChosen[0].dataset.name === cardsChosen[1].dataset.name) {
      cardsMatched.push(cardsChosen);
      
      setTimeout(() => {
        cardsChosen[0].classList.add("match");
        cardsChosen[1].classList.add("match");
        enableCards(); 
        cardsChosen = [];
        resultDisplay.textContent = cardsMatched.length;
        if (cardsMatched.length === emogisArray.length) {
          gameScore( Date.now() - date_start);
          loadScore(game_name);
          document.getElementById("score-title").textContent =
            "Félicitations, vous avez fait correspondre toutes les cartes! 🎉";
        }
      }, 1000);
    } else {
      notMatched();
    }
  }
  
  // Func a ejecutar cuando no hay coincidencia
  function notMatched() {
    disableCardsTemporaly();
  
    setTimeout(() => {
      cardsChosen[0].classList.remove("selected");
      cardsChosen[1].classList.remove("selected");
  
      enableCards();
  
      cardsChosen = [];
    }, 1500);
  }
  
  createGrid();