<template>
    <div>
        <h1>Collaborative Drawing</h1>
        
        <div id="tools">
            <button @click="setTool('text')">Text</button>
            <button @click="setTool('rectangle')">Rectangle</button>
        </div>

        <div id="actions">
            <button @click="deleteSelectedObject">Delete</button>
        </div>
        
        <!-- Canvas -->
        <canvas id="drawingCanvas" width="1024px" height="600px"></canvas>
    </div>
</template>

<script>
import * as fabric from 'fabric';
import { mapState } from 'vuex';
function updateCanvas(data) {
    const canvas = new fabric.Canvas('drawingCanvas');
}


export default {
    data() {
        return {
            clientId: this.generateUUID(),
            canvas: null,
            currentTool: 'draw',
            brushColor: 'black',
            brushWidth: 5,
        };
    },
    mounted() {
        this.canvas = new fabric.Canvas('drawingCanvas');
        this.canvas.isDrawingMode = true;

        this.canvas.selection = true;

        this.canvas.on('object:modified', (e) => {
            const object = e.target;
            this.sendCanvasUpdate(object);
        });

        this.canvas.on('object:added', (e) => {
            const object = e.target;
            this.sendCanvasUpdate(object);
        });

        Echo.channel('drawing').listen('DrawingEvent', (event) => {
            if (event.origin != this.clientId) {
                this.handleDrawingEvent(event);
            }
        });
    },
    methods: {
        generateUUID() {
            return Math.random().toString(36).substr(2, 9);
        },
        handleDrawingEvent(event) {
            console.log('event received');
            this.setTool(event.tool);
        },
        sendCanvasUpdate(object) {
            const data = {
                id: object.id || null,
                type: object.type,
                left: object.left,
                top: object.top,
                width: object.width,
                height: object.height,
                scaleX: object.scaleX,
                scaleY: object.scaleY,
                angle: object.angle,
                text: object.text || null,
                fill: object.fill || null,
                stroke: object.stroke || null,
            };

            axios.post('/update-drawing', {
                tool: this.currentTool,
                updateData : data,
                origin: this.clientId,
            }).then(() => {
                console.log('Canvas update sent to server.');
            });
        },
        setTool(tool) {
            this.currentTool = tool;

            this.canvas.isDrawingMode = false;
            this.canvas.selection = true;

            if (tool === 'text') {
                let data = {
                    left: 50,
                    top: 50,
                    width: 200,
                    fontSize: 20
                };
                this.addText(data);
            } else if (tool === 'rectangle') {
                let data = {
                    left: 100,
                    top: 50,
                    fill: 'yellow',
                    width: 200,
                    height: 100,
                    objectCaching: false,
                    stroke: this.brushColor,
                    strokeWidth: 4,
                    hasBorders: true,
                    hasControls: true,
                };
                this.addRectangle(data);
            }
        },
        addText(data) {
            const text = new fabric.Textbox('Type here!', data);
            this.canvas.add(text);
            this.canvas.setActiveObject(text);
        },
        addRectangle(data) {
            const rect = new fabric.Rect(data);

            this.canvas.add(rect);
            this.canvas.setActiveObject(rect);
        },
        deleteSelectedObject() {
            const activeObject = this.canvas.getActiveObject();
            if (activeObject) {
                this.canvas.remove(activeObject);
            }
        },
    },
};
</script>

<style scoped>
#drawingCanvas {
    border: 1px solid #000;
}
button {
    margin: 10px;
    padding: 10px;
    background-color: #f0f0f0;
    border: 1px solid #ccc;
    cursor: pointer;
}
button:hover {
    background-color: #e0e0e0;
}
</style>
