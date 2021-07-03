
Hey! this is a simple crud symfony app. 
Here you will make an album of your favorite artist 🧑‍🎨

## Database - MySql 

▶️ Entities - User and Album (OneToMany User -> Albums)
  
## Requirements 

▶️ Register  
  User Entity with attributes (full name, username, password, role admin/user)  

▶️ Login  
  Register Form ✔️  
  Security.yaml configured (redirects and authentication) ✔️  
  Failed login attempt message with redirect to login route ✔️  
  
▶️ Albums  
  Crud (Only Admin can delete)  ✔️  
  
▶️ Artist List  ✔️
  get artist list form api https://moat.ai/api/task/
  get single artist by id from https://moat.ai/api/task/?id=<artist_id>
