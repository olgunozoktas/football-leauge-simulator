import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/Pages/Leauge/components/ui/tabs"
import LeagueTable from "@/Pages/Leauge/LeagueTable"
import MatchFixtures from "@/Pages/Leauge/MatchFixtures"
import Predictions from "@/Pages/Leauge/Predictions"
import SimulationControls from "@/Pages/Leauge/SimulationControls"
import { TeamProvider } from "@/Pages/Leauge/context/TeamContext"
import FAQ from "@/Pages/Leauge/FAQ"

function App() {
    return (
        <TeamProvider>
            <div className="flex flex-col min-h-screen bg-gradient-to-b from-green-50 to-green-100 dark:from-gray-900 dark:to-gray-800">
                <header className="bg-green-800 text-white shadow-md dark:bg-gray-800">
                    <div className="container mx-auto px-4 py-6">
                        <h1 className="text-3xl font-bold">Insider Champions League</h1>
                        <p className="text-green-200 dark:text-gray-300">Football League Simulation</p>
                    </div>
                </header>

                <main className="container mx-auto px-4 py-8 flex-grow">
                    <Tabs defaultValue="dashboard" className="w-full">
                        <TabsList className="grid w-full grid-cols-4 mb-8">
                            <TabsTrigger value="dashboard">Dashboard</TabsTrigger>
                            <TabsTrigger value="fixtures">Fixtures</TabsTrigger>
                            <TabsTrigger value="predictions">Predictions</TabsTrigger>
                            <TabsTrigger value="faq">FAQ</TabsTrigger>
                        </TabsList>

                        <TabsContent value="dashboard" className="space-y-8">
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                                <div className="md:col-span-2">
                                    <LeagueTable />
                                </div>
                                <div>
                                    <SimulationControls />
                                </div>
                            </div>
                        </TabsContent>

                        <TabsContent value="fixtures">
                            <MatchFixtures />
                        </TabsContent>

                        <TabsContent value="predictions">
                            <Predictions />
                        </TabsContent>

                        <TabsContent value="faq">
                            <FAQ />
                        </TabsContent>
                    </Tabs>
                </main>

                <footer className="bg-green-800 text-white py-6 mt-auto dark:bg-gray-800">
                    <div className="container mx-auto px-4 text-center">
                        <p>© {new Date().getFullYear()} Insider Champions League Simulation</p>
                    </div>
                </footer>
            </div>
        </TeamProvider>
    )
}

export default App
