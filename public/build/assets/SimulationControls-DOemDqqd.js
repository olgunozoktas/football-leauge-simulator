import{j as e}from"./app-DKWSqCLn.js";import{Card as h,CardHeader as x,CardTitle as k,CardContent as u}from"./card-DU3RwQMX.js";import{Button as a}from"./button-4rc5qBUT.js";import{useTeamContext as y}from"./TeamContext-BO8dtYaE.js";import{c as t}from"./createLucideIcon-DyOxXLvW.js";/**
 * @license lucide-react v0.454.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const p=t("Calendar",[["path",{d:"M8 2v4",key:"1cmpym"}],["path",{d:"M16 2v4",key:"4m81vk"}],["rect",{width:"18",height:"18",x:"3",y:"4",rx:"2",key:"1hopcy"}],["path",{d:"M3 10h18",key:"8toen8"}]]);/**
 * @license lucide-react v0.454.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const g=t("FastForward",[["polygon",{points:"13 19 22 12 13 5 13 19",key:"587y9g"}],["polygon",{points:"2 19 11 12 2 5 2 19",key:"3pweh0"}]]);/**
 * @license lucide-react v0.454.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const C=t("Play",[["polygon",{points:"6 3 20 12 6 21 6 3",key:"1oa8hb"}]]);/**
 * @license lucide-react v0.454.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const j=t("RotateCcw",[["path",{d:"M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8",key:"1357e3"}],["path",{d:"M3 3v5h5",key:"1xhq8a"}]]);/**
 * @license lucide-react v0.454.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const N=t("Users",[["path",{d:"M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2",key:"1yyitq"}],["circle",{cx:"9",cy:"7",r:"4",key:"nufk8"}],["path",{d:"M22 21v-2a4 4 0 0 0-3-3.87",key:"kshegd"}],["path",{d:"M16 3.13a4 4 0 0 1 0 7.75",key:"1da9ce"}]]);function S(){const{simulateNextWeek:n,simulateAllWeeks:o,resetSimulation:c,currentWeek:l,isSimulationComplete:r,initializeTeams:m,generateFixtures:d,teams:i,fixtures:s}=y();return e.jsxs(h,{children:[e.jsx(x,{className:"bg-green-700 text-white dark:bg-gray-700",children:e.jsx(k,{children:"Simulation Controls"})}),e.jsx(u,{className:"p-6 pt-4",children:e.jsxs("div",{className:"space-y-4",children:[e.jsxs(a,{className:"w-full",onClick:m,disabled:i.length>0,children:[e.jsx(N,{className:"mr-2 h-4 w-4"}),"Initialize Teams"]}),e.jsxs(a,{className:"w-full",onClick:d,disabled:i.length===0||s.length>0,children:[e.jsx(p,{className:"mr-2 h-4 w-4"}),"Generate Fixtures"]}),e.jsxs(a,{className:"w-full",onClick:n,disabled:r||s.length===0,children:[e.jsx(C,{className:"mr-2 h-4 w-4"}),"Play Next Week"]}),e.jsxs(a,{className:"w-full",variant:"secondary",onClick:o,disabled:r||s.length===0,children:[e.jsx(g,{className:"mr-2 h-4 w-4"}),"Simulate All Weeks"]}),e.jsxs(a,{className:"w-full",variant:"outline",onClick:c,children:[e.jsx(j,{className:"mr-2 h-4 w-4"}),"Reset Simulation"]}),e.jsxs("div",{className:"bg-green-50 p-4 rounded-lg border border-green-200 mt-4 dark:bg-gray-800 dark:border-gray-700",children:[e.jsx("h4",{className:"font-medium mb-2",children:"Simulation Info"}),e.jsx("p",{className:"text-sm text-gray-600 dark:text-gray-300",children:i.length===0?"Click 'Initialize Teams' to create teams.":s.length===0?"Click 'Generate Fixtures' to create match schedule.":r?"Simulation complete! You can reset to start over.":l===0?"Click 'Play Next Week' to start the simulation.":`Currently at Week ${l} of 6. ${6-l} weeks remaining.`})]})]})})]})}export{S as default};
