<html lang="en" xmlns:m="http://www.w3.org/1998/Math/MathML">

<head>
    <meta charset="utf-8">

    <style>

        body {
            margin: 0;
        }

        canvas {
            width: 100%;
            height: 100%
        }

    </style>
</head>
<body>

<div style="position: absolute;">
<button onclick="container.add(clusterLines);">Show Clusters</button>
<button onclick="container.remove(clusterLines);">Hide Custers</button>
</div>

<script src="https://code.jquery.com/jquery-1.4.3.min.js"></script>
<script src="https://unpkg.com/three@0.121.1/build/three.js"></script>
<script src="kmeans2.js"></script>

<script>

    var renderer = new THREE.WebGLRenderer();
    renderer.setSize(window.innerWidth, window.innerHeight);
    document.body.appendChild(renderer.domElement);

    var camera = new THREE.PerspectiveCamera(45, window.innerWidth / window.innerHeight, 1, 500);
    camera.lookAt(new THREE.Vector3(0, 0, 0));

    scene = new THREE.Scene();

    renderer.setSize(window.innerWidth, window.innerHeight);
    document.body.appendChild(renderer.domElement);

    var ranges = [
        [-1, 1],
        [-1, 1],
        [-1, 1]
    ];
    var points = kmeans.generateRandomPoints(ranges, 500);

    var means = kmeans.algorithm(points, 5).means;
    var assignments = kmeans.assignPointsToMeans(points, means);

    var materials = [
        new THREE.LineBasicMaterial({ color:0x1f77b4 }),
        new THREE.LineBasicMaterial({ color:0xff7f0e }),
        new THREE.LineBasicMaterial({ color:0x2ca02c }),
        new THREE.LineBasicMaterial({ color:0xd62728 }),
        new THREE.LineBasicMaterial({ color:0x9467bd })
    ];

    var container = new THREE.Object3D();
    var clusterLines = new THREE.Object3D();
    var pointsHolder = new THREE.Object3D();


    particles = new THREE.Geometry(),
            pMaterial = new THREE.ParticleBasicMaterial({
                color: 0xFFFFFF,
                size:.01
            });


    // create the particle system
    var particleSystem = new THREE.ParticleSystem(
            particles,
            pMaterial);


    for (var i = 0, l = points.length; i < l; i++) {

        var point = points[i];
        var assignment = assignments[i];
        var mean = means[assignment];

        var geometry = new THREE.Geometry();
        geometry.vertices.push(new THREE.Vector3(point[0], point[1], point[2]));
        geometry.vertices.push(new THREE.Vector3(mean[0], mean[1], mean[2]));

        var line = new THREE.Line(geometry, materials[assignment]);

        clusterLines.add(line);


        particle = new THREE.Vertex(
                new THREE.Vector3(point[0], point[1], point[2])
        );

        particles.vertices.push(particle);
    }

    container.add(clusterLines);
    container.add(particleSystem);
    scene.add(container);

    camera.position.z = 5;

    var render = function () {
        requestAnimationFrame(render);

        container.rotation.y += .005;

        renderer.render(scene, camera);
    };

    render();

</script>
</body>
</html>
