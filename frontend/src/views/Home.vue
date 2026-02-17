<template>
  <div class="container mt-4">
    <h2>Welcome to Home Page</h2>

    <button id="save-button">buttom </button>
<div class="grid-container">
  <!-- Top Row -->
  <div class="card">Card 1</div>
  <div class="card">Card 2</div>
  <div class="card">Card 3</div>

  <!-- Middle Main Card -->
  <div class="card main-card">Main Card</div>

  <!-- Bottom Row -->
  <div class="card bottom-card-1">Bottom Card 1</div>
  <div class="card bottom-card-2">Bottom Card 2</div>
</div>
  </div>
  
</template>

<script>
export default {
  name: 'Home'
}

</script>

<style scoped>
.flex-btn {
  display: flex;              /* Makes button content a flex container */
  align-items: center;        /* Vertically center items */
  justify-content: center;    /* Horizontally center items (optional) */
  gap: 8px;                   /* Space between icon and text */
  padding: 10px 20px;         /* Button padding */
  background-color: #007bff;  /* Button background */
  color: white;               /* Text color */
  border: solid;               /* Remove default border */
  border-radius: 15px;          /* Rounded corners */
  cursor: pointer;             /* Pointer on hover */
  font-size: 16px;
  transition: background 0.3s;
}

.flex-btn:hover {
  background-color: #0056b3; /* Darker on hover */
}

  .grid-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr); /* 3 equal columns */
    grid-gap: 20px; /* spacing between cards */
    max-width: 1200px;
    margin: 50px auto;
  }

  .card {
    background-color: #007bff;
    color: white;
    padding: 30px;
    text-align: center;
    border-radius: 8px;
    font-size: 18px;
  }

  /* Main card spanning 3 columns */
  .main-card {
    grid-column: 1 / 4; /* spans all 3 columns */
    background-color: #28a745;
  }

  /* Bottom row with 2 cards: adjust grid */
  .bottom-card-1 {
    grid-column: 1 / 2;
    background-color: #ffc107;
  }
  .bottom-card-2 {
    grid-column: 2 / 4; /* spans 2 columns */
    background-color: #17a2b8;
  }

  /* Optional: responsive */
  @media (max-width: 768px) {
    .grid-container {
      grid-template-columns: 1fr; /* single column on small screens */
    }
    .main-card, .bottom-card-2 {
      grid-column: 1 / 2; /* take full width */
    }
  }
</style>