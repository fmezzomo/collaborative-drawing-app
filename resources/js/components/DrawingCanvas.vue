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
    computed: {
        ...mapState(['drawingData']),
    },
    watch: {
        drawingData(newData) {
            if (newData) {
                this.handleDrawingEvent(newData);
            }
        },
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

        if (this.drawingData) {
            this.handleDrawingEvent(this.drawingData);
        }

        Echo.channel('drawing').listen('DrawingEvent', (event) => {
            if (event.origin !== this.clientId) { 
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
        addTool(tool) {
            axios.post('/change-tool', { tool }).then(() => {
                console.log('Sent to backend.');
            });
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

            if (tool === 'text') {
                this.canvas.isDrawingMode = false;
                this.canvas.selection = true;
                this.addText();
            } else if (tool === 'rectangle') {
                this.canvas.isDrawingMode = false;
                this.canvas.selection = true;
                this.addRectangle();
            }
        },
        addText() {
            const text = new fabric.Textbox('Type here!', {
                left: 50,
                top: 50,
                width: 200,
                fontSize: 20
            });
            this.canvas.add(text);
            this.canvas.setActiveObject(text);
        },
        addRectangle() {
            const rect = new fabric.Rect({
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
            });

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
