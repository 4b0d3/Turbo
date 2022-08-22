import * as THREE from './build/three.module.js';
import { GLTFLoader } from "./jsm/loaders/GLTFLoader.js";

    let scene = new THREE.Scene();

    let camera = new THREE.PerspectiveCamera(75,window.innerWidth/window.innerHeight,0.01,1000);

    let container = document.getElementById( 'webgl' );
    document.body.appendChild( container );

    let renderer = new THREE.WebGLRenderer({antialias:true});
    renderer.setSize(window.innerWidth, window.innerHeight);
    container.appendChild( renderer.domElement );
    
    
    let loader = new GLTFLoader();

    let obj;
    loader.load("./scooterr/source/scene.glb", function(gltf){
        obj = gltf.scene;
        scene.add(obj);
    });
    scene.background = new THREE.Color(0xffffff);
    let light = new THREE.HemisphereLight(0xffffff);
    scene.add(light);
 
    camera.position.set(0,10,100);
    function animate(){
        requestAnimationFrame(animate);
        renderer.render(scene,camera);
    }
    animate();

