const stageWidth = 600;
const stageHeight = 583;

const stage = new Konva.Stage({
  container: 'map',
  width: stageWidth,
  height: stageHeight,
});

const layer = new Konva.Layer();
stage.add(layer);

const imageObj = new Image();
imageObj.src = '../public/images/map.jpg';

const nodos = [
  // Planta baja
  { id: 1, x: 360, y: 510, name: 'Entrada', floor: 'baja' },
  { id: 2, x: 360, y: 400, name: 'Jardineras', floor: 'baja' },
  { id: 3, x: 460, y: 520, name: 'Comedor', floor: 'baja' },
  { id: 4, x: 510, y: 535, name: 'Cafetería', floor: 'baja' },
  { id: 5, x: 420, y: 400, name: 'Letras Batiz', floor: 'baja' },
  { id: 6, x: 480, y: 400, name: 'Auditorio', floor: 'baja' },
  { id: 7, x: 410, y: 450, name: 'Escaleras primer piso', floor: 'baja' },
  { id: 8, x: 440, y: 440, name: 'Baños Auditorio', floor: 'baja' },
  { id: 9, x: 460, y: 480, name: 'Biblioteca', floor: 'baja' },
  { id: 10, x: 330, y: 330, name: 'Papelería Edif. B', floor: 'baja' },
  { id: 11, x: 220, y: 230, name: 'Salones Provisionales', floor: 'baja' },
  { id: 12, x: 140, y: 290, name: 'Canchas Futbol Sala', floor: 'baja' },
  { id: 13, x: 230, y: 184, name: 'Canchas de Básquet', floor: 'baja' },
  { id: 14, x: 250, y: 80, name: 'Bodega Materiales', floor: 'baja' },
  { id: 15, x: 430, y: 265, name: 'Prefectura', floor: 'baja' },
  { id: 16, x: 425, y: 195, name: 'Escaleras primer piso Edif. B', floor: 'baja' },
  { id: 17, x: 360, y: 290, name: 'Acad. Matemáticas', floor: 'baja' },
  { id: 18, x: 335, y: 295, name: 'Acad. Humanísticas', floor: 'baja' },
  { id: 19, x: 300, y: 290, name: 'Servicio Médico', floor: 'baja' },
  { id: 20, x: 370, y: 140, name: 'Salón Máquinas Automat.', floor: 'baja' },
  { id: 21, x: 485, y: 300, name: 'Papelería Ciberbatiz', floor: 'baja' },
  { id: 22, x: 530, y: 310, name: 'Salón Usos Múltiples', floor: 'baja' },
  { id: 23, x: 510, y: 270, name: 'Ciberbatiz', floor: 'baja' },

  // Primer piso
  { id: 24, x: 450, y: 265, name: 'Baño escaleras edificio B', floor: '1' },
  { id: 25, x: 480, y: 230, name: 'Salones 100', floor: '1' },
  { id: 26, x: 325, y: 225, name: 'Salones de dibujo técnico', floor: '1' },
  { id: 27, x: 370, y: 145, name: 'Laboratorio de biología', floor: '1' },
  { id: 28, x: 320, y: 135, name: 'Salones Samsung', floor: '1' },
  { id: 29, x: 450, y: 140, name: 'Laboratorio de sistemas digitales', floor: '1' },
  { id: 30, x: 530, y: 150, name: 'Cubículos de sistemas digitales', floor: '1' },
  { id: 31, x: 420, y: 400, name: 'Gestión escolar', floor: '1' },
  { id: 32, x: 460, y: 400, name: 'Subdirección', floor: '1' },
  { id: 33, x: 490, y: 400, name: 'Dirección', floor: '1' },
  { id: 34, x: 415, y: 480, name: 'Cubículos administración', floor: '1' },
  { id: 35, x: 430, y: 195, name: 'Escaleras edificio B segundo piso', floor: '1' },

  // Segundo piso
  { id: 36, x: 440, y: 230, name: 'Salones 200', floor: '2' },
  { id: 37, x: 430, y: 150, name: 'Laboratorio de programación', floor: '2' },
  { id: 38, x: 475, y: 150, name: 'Laboratorio de base de datos', floor: '2' },
  { id: 39, x: 370, y: 140, name: 'UDI', floor: '2' },
  { id: 40, x: 350, y: 190, name: 'Baño segundo piso edificio B', floor: '2' },
  { id: 41, x: 430, y: 195, name: 'Escaleras tercer piso', floor: '2' },

  // Tercer piso
  { id: 42, x: 440, y: 230, name: 'Salones 300', floor: '3' },
  { id: 43, x: 370, y: 140, name: 'Laboratorio de física', floor: '3' },
  { id: 44, x: 460, y: 150, name: 'Laboratorio de química', floor: '3' },
  { id: 45, x: 425, y: 190, name: 'Escaleras', floor: '3' },
];

const conexiones = [
  { from: 1, to: 2 }, { from: 1, to: 3 }, { from: 3, to: 4 },
  { from: 2, to: 5 }, { from: 5, to: 8 }, { from: 5, to: 6 },
  { from: 5, to: 7 }, { from: 5, to: 9 }, { from: 2, to: 10 },
  { from: 10, to: 11 }, { from: 11, to: 12 }, { from: 11, to: 13 },
  { from: 13, to: 14 }, { from: 2, to: 15 }, { from: 15, to: 17 },
  { from: 17, to: 18 }, { from: 18, to: 19 }, { from: 15, to: 16 },
  { from: 16, to: 20 }, { from: 15, to: 21 }, { from: 21, to: 22 },
  { from: 22, to: 23 }, { from: 16, to: 35 }, { from: 35, to: 25 },
  { from: 25, to: 26 }, { from: 35, to: 24 }, { from: 35, to: 27 },
  { from: 27, to: 28 }, { from: 35, to: 29 }, { from: 29, to: 30 },
  { from: 35, to: 41 }, { from: 41, to: 40 }, { from: 41, to: 36 },
  { from: 41, to: 37 }, { from: 37, to: 39 }, { from: 37, to: 38 },
  { from: 41, to: 45 }, { from: 45, to: 44 }, { from: 45, to: 42 },
  { from: 7, to: 31 }, { from: 31, to: 32 }, { from: 32, to: 33 },
  { from: 31, to: 34 }
];

const escalerasPorPiso = {
  baja: 7,
  1: 35,
  2: 41,
  3: 45
};

let rutaLine = null; // Línea activa

function getNodo(id) {
  return nodos.find(n => n.id === id);
}

function getVecinos(id) {
  return conexiones
    .filter(c => c.from === id)
    .map(c => c.to)
    .concat(
      conexiones.filter(c => c.to === id).map(c => c.from)
    );
}

// BFS: ruta más corta
function buscarRuta(origen, destino) {
  const cola = [[origen]];
  const visitados = new Set();

  while (cola.length > 0) {
    const camino = cola.shift();
    const actual = camino[camino.length - 1];
    if (actual === destino) return camino;

    if (!visitados.has(actual)) {
      visitados.add(actual);
      getVecinos(actual).forEach(vecino => {
        if (!visitados.has(vecino)) {
          cola.push([...camino, vecino]);
        }
      });
    }
  }
  return null;
}

function construirRuta(idDestino) {
  const destino = getNodo(idDestino);
  if (!destino) return;

  let rutaFinal = [];

  if (destino.floor === 'baja') {
    rutaFinal = buscarRuta(1, idDestino); // Solo desde Entrada
  } else {
    // Ruta completa desde Entrada → Esc. Baja → Esc. Piso → Destino
    const escBaja = escalerasPorPiso['baja'];
    const escPiso = escalerasPorPiso[destino.floor];

    const tramo1 = buscarRuta(1, escBaja);
    const tramo2 = [escBaja, escPiso]; // salto entre escaleras
    const tramo3 = buscarRuta(escPiso, idDestino);

    if (tramo1 && tramo3) {
      rutaFinal = [...tramo1, ...tramo2.slice(1), ...tramo3.slice(1)];
    }
  }

  dibujarRuta(rutaFinal);
}

function dibujarRuta(caminoIds) {
  if (rutaLine) {
    rutaLine.destroy();
    rutaLine = null;
  }
  const puntos = [];
  caminoIds.forEach(id => {
    const nodo = getNodo(id);
    if (nodo) {
      puntos.push(nodo.x, nodo.y);
    }
  });

  rutaLine = new Konva.Line({
    points: puntos,
    stroke: 'green',
    strokeWidth: 4,
    lineCap: 'round',
    lineJoin: 'round',
  });

  layer.add(rutaLine);
  layer.draw();
}


imageObj.onload = () => {
  const background = new Konva.Image({
    x: 0, y: 0,
    image: imageObj,
    width: stageWidth,
    height: stageHeight,
  });
  layer.add(background);
  drawNodes();
  layer.draw();
};

function drawNodes() {
  nodos.forEach(nodo => {
    const grupo = new Konva.Group({
      x: nodo.x,
      y: nodo.y,
      name: 'nodo',
      nodeId: nodo.id,
      floor: nodo.floor
    });

    const circle = new Konva.Circle({
      radius: 12,
      fill: nodo.name.toLowerCase().includes('escalera') ? 'red' : 'deepskyblue',
      stroke: 'black',
      strokeWidth: 1.5,
    });

    const numberText = new Konva.Text({
      text: nodo.id.toString(),
      fontSize: 12,
      fontFamily: 'Calibri',
      fill: 'white',
      align: 'center',
      verticalAlign: 'middle',
      width: 24,
      offsetX: 12,
      offsetY: 8,
    });

    const label = new Konva.Label({
      opacity: 0.85,
      visible: false,
    });
    label.add(new Konva.Tag({
      fill: 'black',
      pointerDirection: 'down',
      pointerWidth: 8,
      pointerHeight: 8,
      lineJoin: 'round',
      cornerRadius: 4,
    }));
    const labelText = new Konva.Text({
      text: nodo.name,
      fontSize: 12,
      fontFamily: 'Calibri',
      padding: 4,
      fill: 'white'
    });
    label.add(labelText);
    layer.add(label);

    grupo.on('mouseenter', () => {
      document.body.style.cursor = 'pointer';
      const pos = grupo.getClientRect();
      label.position({
        x: pos.x + pos.width / 2,
        y: pos.y - 5,
      });
      label.show();
      layer.batchDraw();
    });
    grupo.on('mouseleave', () => {
      document.body.style.cursor = 'default';
      label.hide();
      layer.batchDraw();
    });

    grupo.on('click', () => {
      construirRuta(nodo.id);
    });


    grupo.add(circle);
    grupo.add(numberText);
    layer.add(grupo);
  });
}

document.getElementById('map-buttons').addEventListener('click', e => {
  if (e.target.nodeName !== 'BUTTON') return;
  const floor = e.target.getAttribute('data-floor');
  stage.find('.nodo').forEach(grp => {
    if (floor === 'all' || grp.getAttr('floor') === floor) {
      grp.show();
    } else {
      grp.hide();
    }
  });
  layer.draw();
});
