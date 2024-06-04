#include <iostream>
using namespace std;

int main()
{
    double grade;
    cout << "Enter a Grade: ";
    cin >> grade;

    // debugging statement to check the value read
    cout << "You entered: " << grade << endl;

    if (grade >= 75)
    {
        cout << grade << " - Passed" << endl;
    }
    else
    {
        cout << grade << " - Failed" << endl;
    }

    return 0;
}