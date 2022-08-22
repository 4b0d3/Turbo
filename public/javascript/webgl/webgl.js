import * as THREE from './build/three.module.js';
import { GLTFLoader } from "./jsm/loaders/GLTFLoader.js";
import { OrbitControls } from './jsm/controls/OrbitControls.js';

let HOST = undefined;
if (typeof HOST === 'undefined') {
    HOST = document.documentElement.dataset.host;
}

let scene, camera, renderer, controls;


function init() {
    scene = new THREE.Scene();
    camera = new THREE.PerspectiveCamera( 70, window.innerWidth/2.1 / window.innerHeight, 1, 1000 );
    camera.position.z = 400;
    
    renderer = new THREE.WebGLRenderer({antialias:true, alpha: true});
    renderer.setSize( window.innerWidth/2.1, window.innerHeight );
    renderer.setPixelRatio( window.devicePixelRatio );

    controls = new OrbitControls( camera, renderer.domElement );
    controls.target = new THREE.Vector3(0,8,0);
    controls.enablePan = false;
    controls.autoRotate = true;
    
    let container = document.getElementById( 'webgl' );
    container.appendChild( renderer.domElement );
    
    let loader = new GLTFLoader();
    loader.load(HOST + "javascript/webgl/scooterr/source/scene.glb", function(gltf){
        const model = gltf.scene;
        scene.add(model);
    });
    
    let light = new THREE.HemisphereLight(0xffffff);
    scene.add(light);
    
    camera.position.set(0,8,20);

    window.addEventListener( 'resize', onWindowResize );
}


function animate(){

    requestAnimationFrame(animate);
    controls.update();
    renderer.render( scene, camera);
}

function onWindowResize() {

    camera.aspect = window.innerWidth/2.1 / window.innerHeight;
    camera.updateProjectionMatrix();

    renderer.setSize( window.innerWidth/2.1, window.innerHeight );
}

init();
animate();

