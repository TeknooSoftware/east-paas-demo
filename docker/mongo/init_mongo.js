conn = new Mongo();
db = conn.getDB("paas");

db.createUser(
  {
    user: "paas_user",
    pwd: "paas_pwd",
    roles: [
      { role: "readWrite", db: "paas" }
    ],
    "mechanisms" : ["SCRAM-SHA-256"],
    "passwordDigestor": "server"
  }
);

db.users.insert(
  {
    "_id": "92c70ddccbda3c85985d84e70037f218",
    "created_at": "2017-09-14T22:19:32.775Z",
    "deleted_at": null,
    "email": "paas@teknoo.software",
    "first_name": "PaaS",
    "last_name": "Admin",
    "password": "e57ec31cdabdaee75579a206cf3154e72d6f3776389a962b7fe5f69888195019372ac872856ab79765930002612f6e8e983ed0148638a9f937b1b961f6bf29cc",
    "roles": ["ROLE_USER", "ROLE_ADMIN"],
    "salt":  "9095fe9d8723a08ecdda360803577f7337130a39",
    "updated_at": "2020-09-03T10:59:30.369Z"
  }
);