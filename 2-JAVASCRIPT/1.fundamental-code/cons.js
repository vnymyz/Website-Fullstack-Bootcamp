// constructor function
function User(name, age) {
  this.name = name;
  this.age = age;
}

// create object
const user1 = new User("Vanya", 20);
const user2 = new User("Budi", 18);

console.log(user1.name); // Vanya
console.log(user2.age); // 18
console.log(user2.name); // User { name: 'Vanya', age: 20 }
