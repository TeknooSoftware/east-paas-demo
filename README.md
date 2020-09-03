Teknoo Software - PaaS server example
======================================

Example of PaaS server thanks to [Teknoo East PaaS library project](https://github.com/TeknooSoftware/east-paas).

A `docker-compose` file and docker images in the folder `docker` are available to run the project.
To start the project ` docker-compose up -d --build`

To access on the admin, go to `https://localhost/admin`, the email is `paas@teknoo.software` and password `paastest`.

A docker personal registry is needed. You can set up one by following this [instructions](https://docs.docker.com/registry/deploying/).
Or with this command :
    
    docker run  -d 
                --restart=always 
                --name registry 
                -v /path/to/persist/registry:/var/lib/registry 
                -v /path/to/folder/auth:/auth 
                -e "REGISTRY_AUTH=htpasswd" 
                -e "REGISTRY_AUTH_HTPASSWD_REALM=Paas Pwd" 
                -e REGISTRY_AUTH_HTPASSWD_PATH=/auth/htpasswd #htpasswd file, can be created with apache's tools 
                -v /path/to/certs:/certs 
                -e REGISTRY_HTTP_ADDR=0.0.0.0:443 
                -e REGISTRY_HTTP_TLS_CERTIFICATE=/certs/registry.crt #certificate, see docker instructions
                -e REGISTRY_HTTP_TLS_KEY=/certs/registry.key 
                -p 443:443 
                registry:2
                
If you use self-signed certificate, you must copy certificate in the folder `certs.d` available at the root of this project. 
In a folder, called like the hostname or ip.
                
A Kubernetes cluster is also need, you can use Minikube. You must enable Minikube's addon `registry-creds` to configure
to use your docker registry.

Warning, if your registry use self-signed certificate, you must also add certificate to minikube :

    cat /path/to/registry.crt | minikube ssh "sudo mkdir -p /etc/docker/certs.d/hostname_registry && sudo tee /etc/docker/certs.d/hostname_registry/ca.crt"

After create a new account and a project in the admin, you can start a new project by calling

    https://localhost/project/{projectId}/environment/{envName}/job/create
    
And pass in the request body, as JSON dictionary/object, variables needed to create a job, by example for a the demo project
    
    {
        FOO: "bar",
        COMPOSER: "install"
    }

To run the job

    https://localhost/project/{projectId}/environment/{envName}/job/{jobId}/create

Support this project
---------------------

This project is free and will remain free, but it is developed on my personal time. 
If you like it and help me maintain it and evolve it, don't hesitate to support me on [Patreon](https://patreon.com/teknoo_software).
Thanks :) Richard. 

Credits
-------
Richard Déloge - <richarddeloge@gmail.com> - Lead developer.
Teknoo Software - <https://teknoo.software>

About Teknoo Software
---------------------
**Teknoo Software** is a PHP software editor, founded by Richard Déloge.
Teknoo Software's goals : Provide to our partners and to the community a set of high quality services or software,
 sharing knowledge and skills.

License
-------
East PaaS is licensed under the MIT License - see the licenses folder for details
