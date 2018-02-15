
$(document).ready(function(){
    diagram = new go.Diagram("draw");
    //diagram.contentAlignment=go.Spot.AllSides;
    $$ = go.GraphObject.make;

});

function drawFromModel(model,width,height){

    //Convertimos el width y height a valores numericos. Puede venir con % o con px
    width=width.search("%")>=0?parseInt(width)/100*diagram.viewportBounds.width-30:parseInt(width);
    height=height.search("%")>=0?parseInt(height)/100*diagram.viewportBounds.height-30:parseInt(height);

    //Parche para que gojs no haga un scrolling raro cuando el tamaño del diagrama es menor que el viewport.
    if(diagram.viewportBounds.width>width) width=diagram.viewportBounds.width-30;
    if(diagram.viewportBounds.height>height) height=diagram.viewportBounds.height-30;

    diagram.fixedBounds=new go.Rect(0,0,width,height);

    diagram.allowClipboard=false;

      //diagram.grid.visible=true;
      diagram.grid =$$(go.Panel, go.Panel.Grid,
        { gridCellSize: new go.Size(50, 50) },
            $$(go.Shape, "LineH", { stroke: "#C1D8E5" }),
            $$(go.Shape, "LineV", { stroke: "#C1D8E5" }));
      diagram.toolManager.draggingTool.isGridSnapEnabled = true;
      diagram.toolManager.draggingTool.gridSnapCellSize = new go.Size(10, 10);

      diagram.nodeTemplate = $$(go.Node,
        go.Panel.Spot,
        new go.Binding("location", "loc"),
        { fromSpot: go.Spot.Bottom, toSpot: go.Spot.Top },
        //main
        $$(go.Panel, go.Panel.Auto,$$(go.Shape,
            { figure: "RoundedRectangle", stroke: null },new go.Binding("fill","status",function(s){if(s=="completed") return "green"; else if(s=="current") return "goldenrod"; else return "#006699";})),
            $$(go.TextBlock,{stroke: "white", margin: new go.Margin(10,30,10,30)},new go.Binding("text", "name"))),
        // decorations:
        $$(go.Shape, "Circle",
            { alignment: go.Spot.BottomCenter,
            fill: "#FCC87B", width: 14, height: 14,
            visible: false },
        new go.Binding("visible", "stop")),
        $$(go.Shape, "Circle",
            { alignment: go.Spot.TopCenter,
            fill: "#C7EFA2", width: 14, height: 14,
            visible: false },
            new go.Binding("visible", "start"))
        );

      diagram.linkTemplate = $$(go.Link,
      { routing: go.Link.AvoidsNodes, corner: 5, curve: go.Link.JumpOver, toEndSegmentLength: 30, fromEndSegmentLength: 30 },  // link route should avoid nodes
      $$(go.Shape, {strokeWidth: 2}),
      $$(go.Picture, { segmentIndex: 0, segmentOffset: new go.Point(12, 0)  },new go.Binding("source", "type", function(v){return v!="union"?base_url+"assets/img/"+v+".gif":""})),
      $$(go.Picture, { segmentIndex: -1, segmentOffset: new go.Point(-12, 0)  },new go.Binding("source", "type", function(v){return v=="union"?base_url+"assets/img/"+v+".gif":""})),
      $$(go.Shape, { toArrow: "Standard" }));

  var nodeDataArray=new Array();
  for(var i in model.elements){
      nodeDataArray.push({
          key: model.elements[i].id,
          name: model.elements[i].name,
          loc: new go.Point(parseInt(model.elements[i].left),parseInt(model.elements[i].top)),
          start: model.elements[i].start==1?true:false,
          stop: false,
          status: "pending"
      });
  }
  //console.log(nodeDataArray);
  var linkDataArray = new Array();
  for(var i in model.connections){
      if(model.connections[i].target==null){
          for(var j in nodeDataArray){
              if(nodeDataArray[j].key==model.connections[i].source)
                  nodeDataArray[j].stop=true;
          }
      }else{
      linkDataArray.push({
          from: model.connections[i].source,
          to: model.connections[i].target,
          type: model.connections[i].tipo
      });
      }
  }

  diagram.model = new go.GraphLinksModel(nodeDataArray, linkDataArray);
}
