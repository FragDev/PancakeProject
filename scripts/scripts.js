function decrement(e) {
  const btn = e.target.parentNode.parentElement.querySelector(
    'button[data-action="decrement"]'
  );
  const target = btn.nextElementSibling;
  let value = Number(target.value);
  value--;
  if (value >= 1) {
    target.value = value;
    decrementPancake();
  }
}

function increment(e) {
  const btn = e.target.parentNode.parentElement.querySelector(
    'button[data-action="decrement"]'
  );
  const target = btn.nextElementSibling;
  let value = Number(target.value);
  value++;
  target.value = value;
    incrementPancake();
}

function updatePancakes(e) {
  if (e.target.value < 1) {
    e.target.value = 1
    pancakeStack.querySelectorAll('.pancake').forEach(pancake => pancake.remove());
    incrementPancake();
  } else {
    pancakeStack.querySelectorAll('.pancake').forEach(pancake => pancake.remove());
    for (var i = 0; i < e.target.value; i++) {
      incrementPancake();
    }
  }
}

const pancakeStack = document.getElementById('pancakes-stack');

const inputs = document.querySelectorAll(`input[name="custom-input-number"]`);

const decrementButtons = document.querySelectorAll(
  `button[data-action="decrement"]`
);

const incrementButtons = document.querySelectorAll(
  `button[data-action="increment"]`
);

inputs.forEach(input => {
  input.addEventListener('change', updatePancakes);
});

decrementButtons.forEach(btn => {
  btn.addEventListener("click", decrement);
});

incrementButtons.forEach(btn => {
  btn.addEventListener("click", increment);
});

function getPancake() {
  var pancake = document.createElement('div');
  pancake.className = 'pancake';
  return pancake;
}

function incrementPancake() {
  pancakeStack.appendChild(getPancake());
}

function decrementPancake() {
  pancakeStack.querySelectorAll('.pancake')[0].remove();
}
